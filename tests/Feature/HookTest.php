<?php

namespace Tests\Feature;

use App\WebhookEndpoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\User;

class HookTest extends TestCase
{

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
      $this->testUser = User::create([
        'username' => 'testUser',
        'uptoken' => 'abc123',
        'password' => 'abc123'
      ]);
    }
    
    protected function tearDown(): void {
      $this->testUser->delete();
      parent::tearDown();
    }

    private function createHook(string $type = 'json_post'): WebhookEndpoint {
      $userid = $this->testUser->value('id');
      $hookdata = [
        'user_id' => $userid,
        'description' => 'test hook',
        'upid' => 'abc-123',
        'secret_key' => '5891b5b522d5df086d0ff0b110fbd9d21bb4fc7163af34d08286a2e846f6be03',
        'action_type' => $type,
        'action_url' => 'https://httpbin.org/post'
      ];

      if($type === 'http_get') {
        $hookdata['action_url'] = 'https://httpbin.org/get';
      }

      return WebhookEndpoint::create($hookdata);
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
    }
}