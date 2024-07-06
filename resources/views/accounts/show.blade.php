@extends('layout')
@section('title', $account->attributes->displayName)

@section('content')
<div class="w-3/4">
  <x-account
              :name="$account->attributes->displayName"
              :balance="$account->attributes->balance->value"
              :upid="$account->id"
              />

  @forelse ($transactions as $transaction)
    <x-transaction
      :transaction="$transaction"
      />
  @empty
    No transactions to show.
  @endforelse
</div>
@endsection