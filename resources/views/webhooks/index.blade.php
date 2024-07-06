@extends('layout')
@section('title', 'Webhooks')

@section('content')
<div class="w-3/4">

  <div class="text-right">
    <a href="{{ route('webhooks.create') }}" class="btn-action">
      Create new webhook
    </a>
  </div>

  @forelse ($webhooks as $webhook)
    <x-webhook
      :webhook="$webhook"
      />
  @empty
    No webhooks to show.
  @endforelse
</div>
@endsection