@extends('layout')
@section('title', 'Webhooks')

@section('content')
<div class="w-3/4">

  <div class="text-right p-4">
    <a href="{{ route('webhooks.create') }}" class="btn-action">
      Create new webhook
    </a>
  </div>

  <div class="shadow overflow-hidden rounded border-b border-gray-200">
    <table class="min-w-full bg-white">
      <thead class="bg-gray-800 text-white">
        <tr>
          <th class="w-1/3 text-left py-3 px-4 uppercase font-semibold text-sm">Description</th>
          <th class="w-1/3 text-left py-3 px-4 uppercase font-semibold text-sm">Created</th>
          <th class="text-right py-3 px-4 uppercase font-semibold text-sm">Actions</th>
        </tr>
      </thead>
    <tbody class="text-gray-700">
  @forelse ($webhooks as $i=>$webhook)
      <!-- @json($webhook) -->
      <tr class="{{ ($i%2)?'bg-gray-100':''}}">
        <td class="w-1/3 text-left py-3 px-4">{{ $webhook->description }}</td>
        <td class="w-1/3 text-left py-3 px-4">{{ $webhook->created_at }}</td>
        <td class="text-right py-3 px-4">
    @if($webhook->id)
          <a class="btn-action" href="{{ route('webhooks.ping', $webhook->id) }}">Ping</a>
          <a class="btn-action" href="{{ route('webhooks.delete', $webhook->id) }}">Delete</a>
    @else
          <a class="btn-action" href="{{ route('webhooks.serverdelete', $webhook->upid) }}">Delete from server</a>
    @endif
        </td>
      </tr>
  @empty
    No webhooks to show.
  @endforelse
    </tbody>
  </table>
</div>
@endsection