<?php

namespace Tests\Feature;

use App\WebhookEndpoint;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\User;

/**
 * Covers the mass-assignment guard on WebhookEndpoint and the ownership
 * checks on WebhooksController.
 *
 * None of these hit the network: every case either aborts before the Up API
 * is reached, or exercises a route that never calls it.
 */
class WebhookAuthorizationTest extends TestCase
{
    private $owner;
    private $attacker;
    private $hook;

    private const SECRET = '5891b5b522d5df086d0ff0b110fbd9d21bb4fc7163af34d08286a2e846f6be03';

    protected function setUp(): void {
      parent::setUp();

      // Every case here should abort before any outbound call. Faking makes
      // that a guarantee rather than an argument about control flow.
      Http::fake();

      $this->owner = User::create([
        'username' => 'hookOwner',
        'uptoken' => 'up:yeah:owner',
        'password' => 'abc123'
      ]);
      $this->attacker = User::create([
        'username' => 'hookAttacker',
        'uptoken' => 'up:yeah:attacker',
        'password' => 'abc123'
      ]);

      $this->hook = new WebhookEndpoint([
        'description' => 'owned hook',
        'action_type' => 'json_post',
        'action_url' => 'https://example.com/hook'
      ]);
      $this->hook->user_id = $this->owner->id;
      $this->hook->upid = 'abc-123';
      $this->hook->secret_key = self::SECRET;
      $this->hook->save();
    }

    protected function tearDown(): void {
      WebhookEndpoint::where('user_id', $this->owner->id)->delete();
      WebhookEndpoint::where('user_id', $this->attacker->id)->delete();
      $this->owner->delete();
      $this->attacker->delete();
      parent::tearDown();
    }

    /**
     * The guarded columns must not be settable through the constructor.
     *
     * @return void
     */
    public function testGuardedAttributesAreNotMassAssignable()
    {
      $hook = new WebhookEndpoint([
        'description' => 'mass assigned',
        'action_type' => 'json_post',
        'action_url' => 'https://example.com/hook',
        'user_id' => $this->attacker->id,
        'upid' => 'attacker-upid',
        'secret_key' => 'attacker-secret'
      ]);

      $this->assertNull($hook->user_id);
      $this->assertNull($hook->upid);
      $this->assertNull($hook->secret_key);

      // The legitimate fields still come through.
      $this->assertEquals('mass assigned', $hook->description);
      $this->assertEquals('json_post', $hook->action_type);
      $this->assertEquals('https://example.com/hook', $hook->action_url);
    }

    /**
     * fill() on an existing model must not rewrite the guarded columns.
     *
     * @return void
     */
    public function testGuardedAttributesSurviveFill()
    {
      $this->hook->fill([
        'description' => 'renamed',
        'user_id' => $this->attacker->id,
        'secret_key' => 'attacker-secret'
      ]);

      $this->assertEquals('renamed', $this->hook->description);
      $this->assertEquals($this->owner->id, $this->hook->user_id);
      $this->assertEquals(self::SECRET, $this->hook->secret_key);
    }

    /**
     * The owner cannot reassign a hook to another user over HTTP. This is the
     * step that let a forged webhook be processed with the victim's Up token.
     *
     * @return void
     */
    public function testUpdateCannotReassignOwnership()
    {
      $response = $this->actingAs($this->owner)->patch("/webhooks/{$this->hook->id}", [
        'description' => 'still mine',
        'action_type' => 'json_post',
        'action_url' => 'https://attacker.example/collect',
        'user_id' => $this->attacker->id
      ]);

      $response->assertStatus(302);

      $this->hook->refresh();
      $this->assertEquals($this->owner->id, $this->hook->user_id);
      $this->assertEquals('still mine', $this->hook->description);
    }

    /**
     * The signing key must not be settable over HTTP - controlling it lets an
     * attacker forge a valid X-Up-Authenticity-Signature.
     *
     * @return void
     */
    public function testUpdateCannotOverwriteSecretKey()
    {
      $response = $this->actingAs($this->owner)->patch("/webhooks/{$this->hook->id}", [
        'description' => 'owned hook',
        'action_type' => 'json_post',
        'action_url' => 'https://example.com/hook',
        'secret_key' => 'attacker-chosen-key',
        'upid' => 'attacker-upid'
      ]);

      $response->assertStatus(302);

      $this->hook->refresh();
      $this->assertEquals(self::SECRET, $this->hook->secret_key);
      $this->assertEquals('abc-123', $this->hook->upid);
    }

    /**
     * The guard must not have broken ordinary edits.
     *
     * @return void
     */
    public function testOwnerCanStillUpdateTheirOwnHook()
    {
      $response = $this->actingAs($this->owner)->patch("/webhooks/{$this->hook->id}", [
        'description' => 'new description',
        'action_type' => 'discord',
        'action_url' => 'https://discord.com/api/webhooks/1/abc'
      ]);

      $response->assertStatus(302);

      $this->hook->refresh();
      $this->assertEquals('new description', $this->hook->description);
      $this->assertEquals('discord', $this->hook->action_type);
      $this->assertEquals('https://discord.com/api/webhooks/1/abc', $this->hook->action_url);
    }

    /**
     * @return void
     */
    public function testNonOwnerCannotUpdateHook()
    {
      $response = $this->actingAs($this->attacker)->patch("/webhooks/{$this->hook->id}", [
        'description' => 'hijacked',
        'action_type' => 'json_post',
        'action_url' => 'https://attacker.example/collect'
      ]);

      $response->assertStatus(403);

      $this->hook->refresh();
      $this->assertEquals('owned hook', $this->hook->description);
    }

    /**
     * test() used to log a warning and carry on, letting any authenticated
     * user fire any hook.
     *
     * @return void
     */
    public function testNonOwnerCannotTriggerHook()
    {
      $response = $this->actingAs($this->attacker)->get("/webhooks/{$this->hook->id}/test");

      $response->assertStatus(403);
    }

    /**
     * @return void
     */
    public function testNonOwnerCannotViewHook()
    {
      $response = $this->actingAs($this->attacker)->get("/webhooks/{$this->hook->id}");

      $response->assertStatus(403);
    }

    /**
     * @return void
     */
    public function testNonOwnerCannotDestroyHook()
    {
      $response = $this->actingAs($this->attacker)->delete("/webhooks/{$this->hook->id}");

      $response->assertStatus(403);
      $this->assertNotNull(WebhookEndpoint::find($this->hook->id));
    }

    /**
     * A transaction id that isn't a UUID must be rejected before it reaches
     * the Up API, where "../" would resolve to a different endpoint.
     *
     * @return void
     */
    public function testHandleRejectsNonUuidTransactionId()
    {
      $payload = [
        'data' => [
          'type' => 'webhook-events',
          'id' => '6d51b963-c188-46fb-bbc2-d417cddb2b90',
          'attributes' => [
            'eventType' => 'TRANSACTION_CREATED',
            'createdAt' => '2020-08-13T17:50:20+10:00'
          ],
          'relationships' => [
            'transaction' => [
              'data' => [
                'type' => 'transactions',
                'id' => '../accounts'
              ]
            ]
          ]
        ]
      ];

      $signature = hash_hmac('sha256', json_encode($payload), self::SECRET);

      $response = $this->postJson("/hook/test/{$this->hook->id}", $payload, [
                      'X-Up-Authenticity-Signature' => $signature
                    ]);

      $response->assertStatus(400);
    }
}
