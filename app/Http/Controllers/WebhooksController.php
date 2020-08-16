<?php

namespace App\Http\Controllers;

use App\Webhook;
use App\WebhookEndpoint;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $user
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, $user, $id) {
        Log::debug('Handling webhook');
        try {
            $hook = WebhookEndpoint::find($id);
            $received_signature = $request->header(
                'X-Up-Authenticity-Signature'
            );
            $raw_body = file_get_contents('php://input');
            $signature = hash_hmac('sha256', $raw_body, $hook->secret_key);
        
            if (hash_equals($signature, $received_signature)) {
                Log::notice("Valid webhook received: " . $request->getContent());
            } else {
                throw new Exception("Supplied hash does not match");
            }
        } catch(Exception $e) {
            Log::warning($e);
        } finally {
            return response('',200);
        }
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
}
