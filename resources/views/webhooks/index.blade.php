@extends('layout')
@section('title', 'Webhooks')

@section('actions')
<div class="py-1">
  <a href="{{ route('webhooks.create') }}" class="btn-action">
    <i data-feather="plus"></i> Create webhook
  </a>
</div>
@endsection

@section('content')
  <table class="min-w-full webhooks">
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
      <tr class="{{ ($i%2)?'bg-gray-100':''}}">
      @if($webhook->id)
        <td class="expand"><a href="{{ route('webhooks.show', $webhook->id) }}">{{ $webhook->description }}</a></td>
        <td class="expand">{{ $webhook->actionFriendly }}</td>
        <td class="shrink">{{ $webhook->created_at }}</td>
        <td class="text-right shrink">
          <a class="btn-action" href="{{ route('webhooks.ping', $webhook->id) }}" title="Ping"><i data-feather="activity"></i></a>
          <a class="btn-action" href="{{ route('webhooks.test', $webhook->id) }}" title="Test"><i data-feather="cloud-lightning"></i></a>
          <a class="btn-action" href="{{ route('webhooks.edit', $webhook->id) }}" title="Edit"><i data-feather="edit"></i></a>
          <a class="btn-action" href="{{ route('webhooks.delete', $webhook->id) }}" title="Delete"><i data-feather="trash"></i></a>
        </td>
      @else
        <td class="expand stale">Stale webhook on Up: {{ $webhook->description }}</td>
        <td class="shrink"></td>
        <td class="shrink stale">{{ $webhook->timestamp }}</td>
        <td class="text-right shrink">
          <a class="btn-action" href="{{ route('webhooks.serverdelete', $webhook->upid) }}"><i data-feather="trash"></i>Delete from server</a>
        </td>
      @endif
      </tr>
  @empty
    No webhooks to show.
  @endforelse
    </tbody>
  </table>

@endsection
