<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $api = new UpbankAPI(Auth::user()->uptoken);
        try {
            $accounts = $api->getAccounts();
            return view('accounts.index', ['accounts' => $accounts ]);
        } catch(RequestException $e) {
            return view('accounts.index', ['accounts' => [] ])->withErrors("Unable to fetch accounts - " . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $api = new UpbankAPI(Auth::user()->uptoken);
        try {
            $accounts = $api->getAccounts();
            $account = $api->getAccount($id);
            $transactions = $api->getAccountTransactions($id);
            return view('accounts.show', compact(['account', 'transactions', 'accounts']));
        } catch(RequestException $e) {
            return view('accounts.show', ['accounts' => [], 'transactions' => [], 'accounts' => [] ])
                ->withErrors("Unable to fetch account details - " . $e->getMessage());
        }
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
