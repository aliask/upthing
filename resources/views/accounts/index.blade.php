@extends('layout')
@section('title', 'Accounts')

@section('content')
  @foreach ($accounts as $account)    
    <a href="{{ route('accounts.show', ['account' => $account->upid ]) }}">
      <x-accountSummary :account="$account"/>
    </a>
  @endforeach
@endsection