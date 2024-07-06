<?php

namespace App\Http\Controllers;

use App\User;
use App\Webhook;
use App\WebhookEndpoint;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebhooksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $webhooks = WebhookEndpoint::where('user_id', Auth::user()->id)->get();

        // Add the Webhooks that the server knows about
        $api = new UpbankAPI(Auth::user()->uptoken);
        $hooks = $api->getWebhooks();
        foreach($hooks as $webhook) {
            if(WebhookEndpoint::where('upid', $webhook->id)->count() == 0)
                $webhooks->push(new Webhook($webhook->id, $webhook->attributes));
        }

        return view('webhooks.index', compact(['webhooks']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('webhooks.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $endpoint = null;
        try {
            $validatedData = $request->validate([
                'description'   => 'required|max:64',
                'action_type'   => 'required|in:google_script_get,google_script_post,discord',
                'action_url'    => 'required|url'
            ]);

            $endpoint = WebhookEndpoint::create([
                'user_id'       => Auth::user()->id,
                'description'   => $validatedData['description'],
                'action_type'   => $validatedData['action_type'],
                'action_url'    => $validatedData['action_url']
            ]);
            $url = route('webhooks.handle', [
                'user'          => Str::slug(Auth::user()->username, '-'),
                'hookid'        => $endpoint->id
            ]);

            $api = new UpbankAPI(Auth::user()->uptoken);
            $webhook = $api->createWebhook($url, $validatedData['description']);
            $endpoint->upid = $webhook->id;
            $endpoint->secret_key = $webhook->attributes->secretKey;
            $endpoint->save();

            return redirect(route('webhooks.index'))->with("message", "Created webhook!");
        } catch(\Exception $e) {
            Log::error("webhook.store:" . $e->getMessage());
            if($endpoint)
                $endpoint->delete();
            return redirect()->back()->withErrors("Unable to create webhook!");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $webhook = WebhookEndpoint::findOrFail($id);
        return view('webhooks.edit', compact('webhook'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $webhook = WebhookEndpoint::findOrFail($id);
        if($webhook->user_id != Auth::user()->id) {
            abort(401);
        }
        $webhook->update($request->all());
        $webhook->save();
        return redirect(route('webhooks.index'))->with("message", "Webhook updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $webhook = WebhookEndpoint::findOrFail($id);
        if($webhook->user_id != Auth::user()->id) {
            abort(401);
        }
        try {
            $api = new UpbankAPI(Auth::user()->uptoken);
            $api->deleteWebhook($webhook->upid);
            $webhook->delete();
            return redirect(route('webhooks.index'))->with('message', 'Webhook deleted');
        } catch(RequestException $e) {
            $response = $e->response;
            if($response && $response->getStatusCode() == 404) {
                $message = "Server webhook is gone, removed local orphan endpoint";
                $webhook->delete();
            } else {
                $message = "Unable to delete webhook - " . $e->getMessage();
            }
            Log::error($message);
            return redirect(route('webhooks.index'))->withErrors($message);
        }
    }

    /**
     * Display confirmation before destroying webhook
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id) {
        $webhook = WebhookEndpoint::findOrFail($id);
        return view('webhooks.delete', compact('webhook'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $upid
     * @return \Illuminate\Http\Response
     */
    public function serverdestroy($upid)
    {
        try {
            $api = new UpbankAPI(Auth::user()->uptoken);
            $api->deleteWebhook($upid);
            return redirect(route('webhooks.index'))->with('message', 'Webhook deleted');
        } catch(Exception $e) {
            $message = "Unable to delete webhook - " . $e->getMessage();
            Log::error($message);
            return redirect(route('webhooks.index'))->withErrors($message);
        }
    }

    /**
     * Display confirmation before destroying webhook
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function serverdelete($upid) {
        return view('webhooks.serverdelete', compact('upid'));
    }

    /**
     * Process an incoming webhook - checks validity & then calls processHookTransaction
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $user
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, $user, $id) {
        Log::debug('Handling webhook');

        // Handle 404
        $hook = WebhookEndpoint::findOrFail($id);

        // Handle 401
        $received_signature = $request->header(
            'X-Up-Authenticity-Signature'
        ) ?? '';
        $raw_body = $request->getContent();
        $signature = hash_hmac('sha256', $raw_body, $hook->secret_key);
        if (!hash_equals($signature, $received_signature)) {
            abort(401);
        }

        Log::debug("Valid webhook received: " . $raw_body);
        $payload = json_decode($raw_body);
        $hookType = $payload->data->attributes->eventType;
        Log::notice("Received webhook type: $hookType");
        switch($hookType) {
            case 'TRANSACTION_SETTLED':
                $txid = $payload->data->relationships->transaction->data->id;
                $api = new UpbankAPI(User::find($hook->user_id)->uptoken);
                $transaction = $api->getTransaction($txid);
                $this->processHookTransaction($hook, $transaction);
                break;
            case 'PING':
            case 'TRANSACTION_DELETED':
            case 'TRANSACTION_CREATED':
                break;
            default:
                Log::warning("Unknown webhook type received: $hookType");
        }
        return response()->json(["data" => "Webhook processed"]);
    }

    /**
     * Ask Up to send a ping to the requested Webhook
     * 
     * @param int $id Up ID of Webhook to request ping to
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ping($id) {
        $hook = WebhookEndpoint::findOrFail($id);
        if(Auth::user()->id == $hook->user_id) {
            $api = new UpbankAPI(Auth::user()->uptoken);
            $api->pingWebhook($hook->upid);
        } else {
            Log::warning("User does not own this webhook");
        }
        return redirect()->back()->with('message', "Ping requested");
    }

    /**
     * Decides what to do with an incoming Webhook from Up
     * 
     * @param \App\WebhookEndpoint $hook Webhook which triggered this call
     * @param \App\Transaction $transaction Transaction to process
     * @return null
     */
    private function processHookTransaction($hook, $transaction) {
        Log::notice("Performing action for incoming webhook");
        Log::debug("  Webhook: " . json_encode($hook));
        Log::debug("  Transaction: " . json_encode($transaction));

        switch($hook->action_type) {
            case 'google_script_get':
                return $this->sendGoogleScript('get', $hook->action_url, $transaction);
                break;
            case 'google_script_post':
                return $this->sendGoogleScript('post', $hook->action_url, $transaction);
                break;
            case 'discord':
                return $this->sendDiscord($hook->action_url, $transaction);
                break;
            default:
                Log::warning('Not implemented');
        }
        return;
    }

    /**
     * Sends a request to a Google Script: https://developers.google.com/apps-script/guides/web
     * JSON payload needs to be handled by your Google Script (e.g. Insert a line to a spreadsheet)
     * 
     * @param string $method 'get' or 'post' - to trigger doGet() or doPost() respectively 
     * @param string $url The URL of the Google Sheets endpoint (e.g. https://script.google.com/macros/s/{scriptid}/exec)
     * @param \App\Transaction $transaction Transaction to send
     * @return null
     */
    private function sendGoogleScript($method, $url, $transaction) {
        $sendTx = [
            'method'        => 'sendTx',
            'date'          => Carbon::parse($transaction->settledAt)->format('Y-m-d'),
            'description'   => $transaction->description . " (" . $transaction->rawText . ")",
            'category'      => $transaction->category,
            'value'         => $transaction->amount->value
        ];
        Log::debug("Req to $url: " . json_encode($sendTx));
        switch($method) {
            case 'get':
                $response = Http::get($url, $sendTx);
                break;
            case 'post':
                $response = Http::post($url, $sendTx);
                break;
            default:
                Log::warning("Unknown method");
                return;
        }
        Log::debug("Result: " . $response->getBody());
    }

    /**
     * Sends a Webhook to Discord according to the API: https://discord.com/developers/docs/resources/webhook#execute-webhook
     * 
     * @param string $url The URL of the Discord endpoint (e.g. https://discordapp.com/api/webhooks/{id}/{token})
     * @param \App\Transaction $transaction Transaction to send
     * @return null
     */
    private function sendDiscord($url, $transaction) {
        $fields[] = [
            'name' => 'Description',
            'value' => $transaction->description . " (" . $transaction->rawText . ")",
            'inline' => true
        ];  
        $fields[] = [
            'name' => 'Amount',
            'value' => $transaction->amountFormatted,
            'inline' => true
        ];
        $embeds[] = [
            "title" => "UpBank Transaction Settled",
            "type" => "rich",
            "fields" => $fields
        ];
        $payload = ["embeds" => $embeds];
        $response = Http::post($url, $payload);
        Log::debug("Result: " . $response->getBody());
    }

}
