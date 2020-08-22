@extends('layout')
@section('title', 'Edit Webhook')

@section('content')
<form class="w-3/4" action="{{ route('webhooks.update', ['webhook' => $webhook->id]) }}" method="POST">
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
        <option value="google_script_post" {{ $webhook->action_type==="google_script_post"?'selected':'' }}>Google Scripts POST</option>
        <option value="google_script_get" {{ $webhook->action_type==="google_script_get"?'selected':'' }}>Google Scripts GET</option>
        <option value="discord" {{ $webhook->action_type==="discord"?'selected':'' }}>Discord Notification</option>
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