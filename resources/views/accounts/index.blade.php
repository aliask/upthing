@extends('layout')
@section('title', 'Accounts')

@section('content')
    @foreach ($accounts as $account)
        <x-account
            :name="$account->attributes->displayName"
            :balance="$account->attributes->balance->value"
            :upid="$account->id"
            />
    @endforeach
@endsection