<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpbankAPI extends Controller
{

    protected const BASE_URI = 'https://api.up.com.au/api/v1/';

    private $api;

    public function __construct($uptoken)
    {
        $this->api = Http::baseUrl(self::BASE_URI)
                    ->withHeaders(['Authorization' => 'Bearer ' . $uptoken]);
    }

    /**
     * @param integer $pageSize Page size
     * @return object
     */
    public function getAccounts($pageSize = 30) {
        $response = $this->api->get('/accounts', ['page[size]' => $pageSize ]);
        $response->throw();
        $json = json_decode($response->getBody());
        if(isset($json->data))
            return $json->data;
        else
            return new \stdClass();
    }

    /**
     * @param string $upid Account Up ID
     * @return object
     */
    public function getAccount($upid) {
        $response = $this->api->get("/accounts/$upid");
        $response->throw();
        $json = json_decode($response->getBody());
        if(isset($json->data))
            return $json->data;
        else
            return new \stdClass();
    }

    /**
     * @param string $account Account Up ID
     * @param integer $pageSize Page Size
     * @return object
     */
    public function getAccountTransactions($account, $pageSize = 100) {
        $response = $this->api->get("/accounts/$account/transactions", ['page[size]' => $pageSize ]);
        $response->throw();
        $json = json_decode($response->getBody());
        if(isset($json->data))
            return $json->data;
        else
            return new \stdClass();
    }

    /**
     * @param string $transaction Upbank ID
     * @return object
     */
    public function getTransaction($transaction) {
        $response = $this->api->get("/transactions/$transaction");
        $response->throw();
        $json = json_decode($response->getBody());
        if(isset($json->data))
            return $json->data;
        else
            return new \stdClass();
    }

    /**
     * @param integer $pageSize Page size
     * @return object
     */
    public function getWebhooks($pageSize = 30) {
        $response = $this->api->get('/webhooks', ['page[size]' => $pageSize ]);
        $response->throw();
        $json = json_decode($response->getBody());
        if(isset($json->data))
            return $json->data;
        else
            return new \stdClass();
    }

    /**
     * @param string $url
     * @param string $description 
     * @return object
     */
    public function createWebhook($url, $description) {
        $data = ['data' => [ 'attributes' => compact(['url','description']) ]];
        Log::info('UpAPI: Create webhook - ' . json_encode($data));
        $response = $this->api->post('/webhooks', $data);
        Log::debug('UpAPI: Response - ' . $response->getBody());
        $response->throw();
        $json = json_decode($response->getBody());
        if(isset($json->data))
            return $json->data;
        else
            return new \stdClass();
    }

    /**
     * @param string $upid
     * @return object
     */
    public function pingWebhook($upid) {
        Log::info("UpAPI: Ping webhook - $upid");
        $response = $this->api->post("/webhooks/$upid/ping");
        Log::debug('UpAPI: Response - ' . $response->getBody());
        $response->throw();
        $json = json_decode($response->getBody());
        if(isset($json->data))
            return $json->data;
        else
            return new \stdClass();
    }


}
