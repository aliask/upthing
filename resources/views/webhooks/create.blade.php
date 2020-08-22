@extends('layout')
@section('title', 'Create Webhook')

@section('content')
<form class="w-3/4" action="{{ route('webhooks.store') }}" method="POST">
  @csrf
  <div class="form-group">
    <div class="md:w-1/3">
      <label class="form-label" for="description">Description</label>
    </div>
    <div class="md:w-2/3">
      <input required class="form-input" id="description" name="description"
        type="text" maxlength="64" placeholder="Description">
    </div>
  </div>
  <div class="form-group">
    <div class="md:w-1/3">
      <label class="form-label" for="action_type">Action Type</label>
    </div>
    <div class="md:w-2/3">
      <select required class="form-input" id="action_type" name="action_type">
        <option></option>
        <option value="google_script_post">Google Scripts POST</option>
        <option value="google_script_get">Google Scripts GET</option>
        <option value="discord">Discord Notification</option>
      </select>
    </div>
  </div>
  <div class="form-group">
    <div class="md:w-1/3">
      <label class="form-label" for="action_type">Action URL</label>
    </div>
    <div class="md:w-2/3">
      <input required class="form-input" id="action_url" name="action_url"
        type="url" placeholder="Action URL">
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