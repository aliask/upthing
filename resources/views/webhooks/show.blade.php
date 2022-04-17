@extends('layout')
@section('title', 'View Webhook - ' . $webhook->description)

@section('actions')
<div class="py-1">
  <a href="{{ route('webhooks.ping', $webhook->id) }}" class="btn-action">
    <i data-feather="activity"></i> Ping Webhook
  </a>
</div>
<div class="py-1">
  <a href="{{ route('webhooks.test', $webhook->id) }}" class="btn-action">
    <i data-feather="cloud-lightning"></i> Test Webhook
  </a>
</div>
<div class="py-1">
  <a href="{{ route('webhooks.edit', $webhook->id) }}" class="btn-action">
    <i data-feather="edit"></i> Edit Webhook
  </a>
</div>
<div class="py-1">
  <a href="{{ route('webhooks.delete', $webhook->id) }}" class="btn-action">
    <i data-feather="trash"></i> Delete Webhook
  </a>
</div>
@endsection

@section('content')
<table id="logsTable" class="min-w-full">
      <thead>
        <tr>
          <th>Request (from Up)</th>
          <th>Response</th>
          <th>Created</th>
          <th></th>
        </tr>
      </thead>
    <tbody class="text-gray-700">
  @forelse ($logs as $i=>$log)
      <tr class="{{ ($i%2)?'bg-gray-100':''}}">
        <td class="whitespace-pre-wrap font-mono">{{ json_encode(json_decode($log->attributes->request->body), JSON_PRETTY_PRINT) }}</td>
        <td class="shrink">HTTP {{ $log->attributes->response->statusCode }}</td>
        <td class="shrink">{{ $log->attributes->createdAt }}</td>
      </td>
    </tr>
  @empty
    No logs to show.
  @endforelse
    </tbody>
  </table>
@endsection