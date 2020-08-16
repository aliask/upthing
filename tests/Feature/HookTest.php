<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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

    /**
     *
     * @return void
     */
    public function testIncomingPing()
    {
      $payload = json_decode($this->samplePayload);
      $payload->data->attributes->eventType = "PING";
      unset($payload->data->relationships->transaction);

      $signature = hash_hmac('sha256', json_encode($payload), '3jHEZmiCOkOpLldwXRlYHJugFr2KGVObr7gJKEIevzGBNXJc7oeC26q55bt3DkLn');

      $response = $this->postJson('/hook/test/8', (array)$payload, [
                      'X-Up-Authenticity-Signature' => $signature
                    ]);
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

      $signature = hash_hmac('sha256', json_encode($payload), '3jHEZmiCOkOpLldwXRlYHJugFr2KGVObr7gJKEIevzGBNXJc7oeC26q55bt3DkLn');

      $response = $this->postJson('/hook/test/999', (array)$payload, [
                      'X-Up-Authenticity-Signature' => $signature
                    ]);
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

      $signature = hash_hmac('sha256', json_encode($payload), 'Invalid');

      $response = $this->postJson('/hook/test/8', (array)$payload, [
                      'X-Up-Authenticity-Signature' => $signature
                    ]);
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

      $response = $this->postJson('/hook/test/8', (array)$payload);
      $response->assertStatus(401);
    }

    /**
     * Test an incoming transaction notification.
     *
     * @return void
     */
    public function testIncomingTransaction()
    {
      $payload = json_decode($this->samplePayload);
      $payload->data->attributes->eventType = "TRANSACTION_SETTLED";

      $signature = hash_hmac('sha256', json_encode($payload), '3jHEZmiCOkOpLldwXRlYHJugFr2KGVObr7gJKEIevzGBNXJc7oeC26q55bt3DkLn');

      $response = $this->postJson('/hook/test/8', (array)$payload, [
                      'X-Up-Authenticity-Signature' => $signature
                    ]);
      $response->assertStatus(200);
    }
}