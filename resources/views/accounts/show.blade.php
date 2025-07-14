@extends('layout')
@section('title', $account->name)

@section('content')
  <x-accountSummary :account="$account"/>

  @if (count($transactions))
    <div class="px-2 text-l text-right mb-4">
      <a href="{{ route('account.getcsv', $account->upid) }}" class="border border-gray-300 rounded-md px-2 py-1 text-gray-700 hover:bg-gray-100 transition-colors">
        <i data-feather="download"></i> Download CSV
      </a>
    </div>

    <x-transactions :transactions="$transactions"/>
  @else
    No transactions to show.
  @endif
@endsection