<?php

namespace Tests\Feature;

use App\WebhookEndpoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\User;

class HookTest extends TestCase
{
    use RefreshDatabase;


    protected $samplePayload = <<<PAYLOAD
{
  "data": {
    "type": "webhook-events",
    "id": "6d51b963-c188-46fb-bbc2-d417cddb2b90",
    "attributes": {
      "eventType": "TRANSACTION_CREATED",
      "createdAt": "2020-08-13T17:50:20+10:00"
    },
    "relationships": {
      "webhook": {
        "data": {
          "type": "webhooks",
          "id": "0b003be0-f5bf-4aec-8305-4f6cc19e41c7"
        },
        "links": {
          "related": "https://api.up.com.au/api/v1/webhooks/0b003be0-f5bf-4aec-8305-4f6cc19e41c7"
        }
      },
      "transaction": {
        "data": {
          "type": "transactions",
          "id": "a61bb618-fe63-43aa-939a-faf1a57e04ff"
        },
        "links": {
          "related": "https://api.up.com.au/api/v1/transactions/a61bb618-fe63-43aa-939a-faf1a57e04ff"
        }
      }
    }
  }
}
PAYLOAD;

    private $testUser;

    protected function setUp(): void {
      parent::setUp();

      // Nothing in this file should reach the network. The Up API returns one
      // fixed settled transaction; every outbound hook target returns 200.
      Http::fake([
        'api.up.com.au/*' => Http::response($this->upTransactionResponse(), 200),
        '*' => Http::response(['success' => true], 200)
      ]);

      $this->testUser = User::create([
        'username' => 'testUser',
        'uptoken' => 'abc123',
        'password' => 'abc123'
      ]);
    }

    /**
     * A single settled transaction, shaped as the Up API returns it.
     *
     * @return array
     */
    private function upTransactionResponse(): array {
      return [
        'data' => [
          'type' => 'transactions',
          'id' => 'a61bb618-fe63-43aa-939a-faf1a57e04ff',
          'attributes' => [
            'status' => 'SETTLED',
            'rawText' => 'TEST MERCHANT',
            'description' => 'Test Merchant',
            'message' => null,
            'amount' => [
              'currencyCode' => 'AUD',
              'value' => '-12.34',
              'valueInBaseUnits' => -1234
            ],
            'settledAt' => '2020-08-13T17:50:20+10:00',
            'createdAt' => '2020-08-13T17:50:20+10:00'
          ],
          'relationships' => [
            'category' => [
              'data' => [
                'type' => 'categories',
                'id' => 'restaurants-and-cafes'
              ]
            ]
          ]
        ]
      ];
    }
    
    protected function tearDown(): void {
      $this->testUser->delete();
      parent::tearDown();
    }

    private function createHook(string $type = 'json_post'): WebhookEndpoint {
      $hook = new WebhookEndpoint([
        'description' => 'test hook',
        'action_type' => $type,
        'action_url' => $type === 'http_get'
          ? 'https://httpbin.org/get'
          : 'https://httpbin.org/post'
      ]);

      // user_id, upid and secret_key are guarded against mass assignment,
      // so they have to be set directly.
      $hook->user_id = $this->testUser->id;
      $hook->upid = 'abc-123';
      $hook->secret_key = '5891b5b522d5df086d0ff0b110fbd9d21bb4fc7163af34d08286a2e846f6be03';
      $hook->save();

      return $hook;
    }

    /**
     *
     * @return void
     */
    public function testIncomingPing()
    {
      $payload = json_decode($this->samplePayload);
      $payload->data->attributes->eventType = "PING";
      unset($payload->data->relationships->transaction);

      $hook = $this->createHook();
      $signature = hash_hmac('sha256', json_encode($payload), $hook->secret_key);

      $response = $this->postJson("/hook/test/$hook->id", (array)$payload, [
                      'X-Up-Authenticity-Signature' => $signature
                    ]);
      $hook->delete();
      $response->assertStatus(200);
    }

    /**
     *
     * @return void
     */
    public function testIncomingPingWithInvalidId()
    {
      $payload = json_decode($this->samplePayload);
      $payload->data->attributes->eventType = "PING";
      unset($payload->data->relationships->transaction);

      $hook = $this->createHook();
      $signature = hash_hmac('sha256', json_encode($payload), $hook->secret_key);

      $response = $this->postJson('/hook/test/999', (array)$payload, [
                      'X-Up-Authenticity-Signature' => $signature
                    ]);
      $hook->delete();
      $response->assertStatus(404);
    }
    
    /**
     *
     * @return void
     */
    public function testIncomingPingWithInvalidHash()
    {
      $payload = json_decode($this->samplePayload);
      $payload->data->attributes->eventType = "PING";
      unset($payload->data->relationships->transaction);

      $hook = $this->createHook();
      $signature = hash_hmac('sha256', json_encode($payload), 'Invalid');

      $response = $this->postJson("/hook/test/$hook->id", (array)$payload, [
                      'X-Up-Authenticity-Signature' => $signature
                    ]);
      $hook->delete();
      $response->assertStatus(401);
    }
    
    /**
     *
     * @return void
     */
    public function testIncomingPingWithNoHash()
    {
      $payload = json_decode($this->samplePayload);
      $payload->data->attributes->eventType = "PING";
      unset($payload->data->relationships->transaction);

      $hook = $this->createHook();
      $response = $this->postJson("/hook/test/$hook->id", (array)$payload);
      $hook->delete();
      $response->assertStatus(401);
    }

    /**
     * Test an incoming transaction send to HTTP GET.
     *
     * @return void
     */
    public function testIncomingTransactionToHttpGet()
    {
      $payload = json_decode($this->samplePayload);
      $payload->data->attributes->eventType = "TRANSACTION_SETTLED";

      $hook = $this->createHook('http_get');

      $signature = hash_hmac('sha256', json_encode($payload), $hook->secret_key);

      $response = $this->postJson("/hook/test/$hook->id", (array)$payload, [
                      'X-Up-Authenticity-Signature' => $signature
                    ]);
      $hook->delete();
      $response->assertStatus(200);

      Http::assertSent(function ($request) {
        return $request->method() === 'GET'
          && str_starts_with($request->url(), 'https://httpbin.org/get');
      });
    }

    /**
     * Test an incoming transaction send to JSON POST.
     *
     * @return void
     */
    public function testIncomingTransactionToJsonPost()
    {
      $payload = json_decode($this->samplePayload);
      $payload->data->attributes->eventType = "TRANSACTION_SETTLED";

      $hook = $this->createHook('json_post');

      $signature = hash_hmac('sha256', json_encode($payload), $hook->secret_key);

      $response = $this->postJson("/hook/test/$hook->id", (array)$payload, [
                      'X-Up-Authenticity-Signature' => $signature
                    ]);
      $hook->delete();
      $response->assertStatus(200);

      Http::assertSent(function ($request) {
        // sendPost forwards the raw Up transaction, not flattened fields.
        return $request->method() === 'POST'
          && $request->url() === 'https://httpbin.org/post'
          && $request['id'] === 'a61bb618-fe63-43aa-939a-faf1a57e04ff';
      });
    }

    /**
     * Test an incoming transaction send to Discord.
     *
     * @return void
     */
    public function testIncomingTransactionDiscord()
    {
      $payload = json_decode($this->samplePayload);
      $payload->data->attributes->eventType = "TRANSACTION_SETTLED";

      $hook = $this->createHook('discord');

      $signature = hash_hmac('sha256', json_encode($payload), $hook->secret_key);

      $response = $this->postJson("/hook/test/$hook->id", (array)$payload, [
                      'X-Up-Authenticity-Signature' => $signature
                    ]);
      $hook->delete();
      $response->assertStatus(200);

      Http::assertSent(function ($request) {
        return $request->method() === 'POST'
          && $request->url() === 'https://httpbin.org/post'
          && isset($request['embeds']);
      });
    }
}