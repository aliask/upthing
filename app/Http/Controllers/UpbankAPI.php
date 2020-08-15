<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class UpbankAPI extends Controller
{

    protected const BASE_URI = 'https://api.up.com.au/api/v1/';

    public function __construct()
    {
        $this->api = Http::baseUrl(self::BASE_URI)
                    ->withHeaders(['Authorization' => 'Bearer ' . env('UPBANK_PAT')]);
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
        $response = $this->api->post('/webhooks', ['data' => compact(['url','description']) ] );
        $response->throw();
        $json = json_decode($response->getBody());
        if(isset($json->data))
            return $json->data;
        else
            return new \stdClass();
    }


}
