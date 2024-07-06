<?php

namespace App\Http\Controllers;

use App\Webhook;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebhooksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $api = new UpbankAPI(Auth::user()->uptoken);
        $hooks = $api->getWebhooks();
        $webhooks = new Collection();
        foreach($hooks as $webhook) {
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
        try {
            $validatedData = $request->validate([
                'url'           => 'required|url|max:300',
                'description'   => 'required|max:64',
            ]);
            $api = new UpbankAPI(Auth::user()->uptoken);
            $webhook = $api->createWebhook($validatedData['url'], $validatedData['description']);
        } catch(\Exception $e) {
            return redirect()->back()->with("error", "Unable to create webhook: " . json_encode($e));
        }
        return redirect(route('webhook.index'))->with("message", $webhook);
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
}
