@extends('layout')
@section('title', 'Edit Webhook')

@section('content')
<form action="{{ route('webhooks.update', ['webhook' => $webhook->id]) }}" method="POST">
  @csrf
  @method('PATCH')
  <div class="form-group">
    <div class="md:w-1/3">
      <label class="form-label" for="description">Description</label>
    </div>
    <div class="md:w-2/3">
      <input required class="form-input" id="description" name="description"
        type="text" maxlength="64" placeholder="Description" value="{{ $webhook->description }}">
    </div>
  </div>
  <div class="form-group">
    <div class="md:w-1/3">
      <label class="form-label" for="action_type">Action Type</label>
    </div>
    <div class="md:w-2/3">
      <select required class="form-input" id="action_type" name="action_type">
@foreach(\App\WebhookEndpoint::action_types as $type => $action)
        <option value="{{ $type }}" {{ $type==$webhook->action_type?'selected':'' }}>{{ $action['name'] }}</option>
@endforeach
      </select>
    </div>
  </div>
  <div class="form-group">
    <div class="md:w-1/3">
      <label class="form-label" for="action_type">Action URL</label>
    </div>
    <div class="md:w-2/3">
      <input required class="form-input" id="action_url" name="action_url"
        type="url" placeholder="Action URL" value="{{ $webhook->action_url }}">
    </div>
  </div>
  
  <div class="form-group">
    <div class="md:w-1/3"></div>
    <div class="md:w-2/3 text-right">
      <button class="btn-action" type="submit">Edit Webhook</button>
    </div>
  </div>
</form>
@endsection