@extends('layout')
@section('title', $account->name)

@section('content')
<div class="w-3/4">
  <x-accountSummary :account="$account"/>

  @if (count($transactions))
    <x-transactions :transactions="$transactions"/>
  @else
    No transactions to show.
  @endif
</div>
@endsection