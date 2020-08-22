@extends('layout')
@section('title', 'Delete Webhook')

@section('content')
<form action="{{ route('webhooks.destroy', $webhook->id) }}" method="POST">
  @csrf
  @method('delete')
  Are you sure you want to destroy webhook "{{ $webhook->description }}"?
  
  <div class="form-group">
    <div class="md:w-1/3"></div>
    <div class="md:w-2/3 text-right">
      <button class="btn-action" type="submit">Delete Webhook</button>
    </div>
  </div>
</form>
@endsection