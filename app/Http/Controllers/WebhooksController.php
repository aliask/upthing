<?php

namespace App\Http\Controllers;

use App\User;
use App\Webhook;
use App\WebhookEndpoint;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
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
        // $api = new UpbankAPI(Auth::user()->uptoken);
        // $hooks = $api->getWebhooks();
        // $webhooks = new Collection();
        // foreach($hooks as $webhook) {
        //     $webhooks->push(new Webhook($webhook->id, $webhook->attributes));
        // }
        $webhooks = WebhookEndpoint::where('user_id', Auth::user()->id)->get();
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
            ]);

            $endpoint = WebhookEndpoint::create([
                'user_id'       => Auth::user()->id,
                'description'   => $validatedData['description']
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

            return redirect(route('webhook.index'))->with("message", "Created webhook!");
        } catch(\Exception $e) {
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
        //
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
        //
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
            return redirect()->back()->with('message', 'Webhook deleted');
        } catch(Exception $e) {
            return redirect()->back()->withErrors('Unable to delete webhook');
        }
    }

    /**
     * Process an incoming webhook
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
                $this->sendTransaction($transaction);
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

    public function sendTransaction($transaction) {
        Log::debug(json_encode($transaction));
        return;
    }

}
