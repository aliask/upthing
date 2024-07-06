@extends('layout')
@section('title', $account->name)

@section('content')
  <x-accountSummary :account="$account"/>

  @if (count($transactions))
    <x-transactions :transactions="$transactions"/>
  @else
    No transactions to show.
  @endif
@endsection