@extends('layout')
@section('title', 'Webhooks')

@section('content')
<div class="w-3/4">

  <div class="text-right p-4">
    <a href="{{ route('webhooks.create') }}" class="btn-action">
      Create new webhook
    </a>
  </div>

  <table class="min-w-full">
      <thead>
        <tr>
          <th>Description</th>
          <th>Webhook Action</th>
          <th>Created</th>
          <th></th>
        </tr>
      </thead>
    <tbody class="text-gray-700">
  @forelse ($webhooks as $i=>$webhook)
      <!-- @json($webhook) -->
      <tr class="{{ ($i%2)?'bg-gray-100':''}}">
        <td class="expand">{{ $webhook->description }}</td>
        <td class="expand">Test</td>
        <td class="shrink">{{ $webhook->created_at }}</td>
        <td class="text-right shrink p-4">
    @if($webhook->id)
          <a class="btn-action" href="{{ route('webhooks.ping', $webhook->id) }}" title="Ping"><i data-feather="activity"></i></a>
          <a class="btn-action" href="{{ route('webhooks.edit', $webhook->id) }}" title="Edit"><i data-feather="edit"></i></a>
          <a class="btn-action" href="{{ route('webhooks.delete', $webhook->id) }}" title="Delete"><i data-feather="trash"></i></a>
    @else
          <a class="btn-action" href="{{ route('webhooks.serverdelete', $webhook->upid) }}"><i data-feather="trash"></i>Delete from server</a>
    @endif
        </td>
      </tr>
  @empty
    No webhooks to show.
  @endforelse
    </tbody>
  </table>

  @endsection