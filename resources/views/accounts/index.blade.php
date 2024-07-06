@extends('layout')
@section('title', 'Accounts')

@section('content')
    @foreach ($accounts as $account)
        <x-accountSummary :account="$account"/>
    @endforeach
@endsection