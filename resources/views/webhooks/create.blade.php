@extends('layout')
@section('title', 'Create Webhook')

@section('content')
<form class="w-full max-w-sm" action="{{ route('webhooks.store') }}" method="POST">
  @csrf
  <div class="form-group">
    <div class="md:w-1/3">
      <label class="form-label" for="webhook-url">URL</label>
    </div>
    <div class="md:w-2/3">
      <input required class="form-input" id="webhook-url" type="url" maxlength="300" placeholder="Webhook URL">
    </div>
  </div>
  <div class="form-group">
    <div class="md:w-1/3">
      <label class="form-label" for="webhook-description">Description</label>
    </div>
    <div class="md:w-2/3">
      <input required class="form-input" id="webhook-description" type="text" maxlength="64" placeholder="Description">
    </div>
  </div>
  
  <div class="form-group">
    <div class="md:w-1/3"></div>
    <div class="md:w-2/3 text-right">
      <button class="btn-action" type="submit">Create Webhook</button>
    </div>
  </div>
</form>
@endsection