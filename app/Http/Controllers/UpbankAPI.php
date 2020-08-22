<?php

namespace App\Http\Controllers;

use App\Account;
use App\Transaction;
use Exception;
use Illuminate\Support\Collection;
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
     * @param \Illuminate\Http\Client\Response $response Raw response from Up API
     * @return object Data object
     */
    private function processResponse($response) {
        Log::debug('UpAPI: Response - ' . $response->getBody());
        $response->throw();
        $json = json_decode($response->getBody());
        if(!isset($json->data)) {
            throw new Exception("Unexpected format returned by Up API");
        }
        return $json->data;
    }

    /**
     * @param integer $pageSize Page size
     * @return Collection Collection of Account Models
     */
    public function getAccounts($pageSize = 30) {
        Log::info("UpAPI: getAccounts");
        $response = $this->api->get('/accounts', ['page[size]' => $pageSize ]);
        $data = $this->processResponse($response);
        $accounts = new Collection();
        foreach($data as $account) {
            $accounts->push(new Account($account));
        }
        return $accounts;
    }

    /**
     * @param string $upid Account Up ID
     * @return Account Account Model
     */
    public function getAccount($upid) {
        Log::info("UpAPI: getAccount $upid");
        $response = $this->api->get("/accounts/$upid");
        $data = $this->processResponse($response);
        return new Account($data);
    }

    /**
     * @param string $account Account Up ID
     * @param integer $pageSize Page Size
     * @return Collection
     */
    public function getAccountTransactions($account, $pageSize = 100) {
        Log::info("UpAPI: getAccountTransactions $account");
        $response = $this->api->get("/accounts/$account/transactions", ['page[size]' => $pageSize ]);
        Log::debug('UpAPI: Response - ' . $response->getBody());
        $data = $this->processResponse($response);
        $transactions = new Collection();
        foreach($data as $account) {
            $transactions->push(new Transaction($account));
        }
        return $transactions;
    }

    /**
     * @param string $transaction Upbank ID
     * @return Transaction
     */
    public function getTransaction($transaction) {
        Log::info("UpAPI: getTransaction $transaction");
        $response = $this->api->get("/transactions/$transaction");
        $data = $this->processResponse($response);
        return new Transaction($data);
    }

    /**
     * @param integer $pageSize Page size
     * @return object
     */
    public function getWebhooks($pageSize = 30) {
        Log::info("UpAPI: Get webhooks");
        $response = $this->api->get('/webhooks', ['page[size]' => $pageSize ]);
        $data = $this->processResponse($response);
        return $data;
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
        $data = $this->processResponse($response);
        return $data;
    }

    /**
     * @param string $upid
     * @return object
     */
    public function pingWebhook($upid) {
        Log::info("UpAPI: Ping webhook - $upid");
        $response = $this->api->post("/webhooks/$upid/ping");
        $data = $this->processResponse($response);
        return $data;
    }

    public function deleteWebhook($upid) {
        Log::info("UpAPI: Delete webhook - $upid");
        $response = $this->api->delete("/webhooks/$upid");
        $data = $this->processResponse($response);
        return $data;
    }

    public function getHookLogs($upid) {
        Log::info("UpAPI: Get webhook logs - $upid");
        $response = $this->api->get("/webhooks/$upid/logs");
        $data = $this->processResponse($response);
        return $data;
    }


}
