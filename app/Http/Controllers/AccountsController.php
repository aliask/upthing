<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class AccountsController extends UpbankAPI
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = parent::getAccounts();
        return view('accounts.index', ['accounts' => $accounts ]);
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
        $accounts = parent::getAccounts();
        $account = parent::getAccount($id);
        $txs = parent::getAccountTransactions($id);
        $transactions = new Collection();
        foreach($txs as $transaction) {
            $transactions->push(new Transaction($transaction->id, $transaction->attributes));
        }
        return view('accounts.show', compact(['account', 'transactions', 'accounts']));
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
