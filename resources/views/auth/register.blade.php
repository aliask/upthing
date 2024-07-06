@extends('layout')
@section('title', 'Register')

@section('content')
<form class="w-1/2" action="{{ route('register') }}" method="POST">
  @csrf
  <div class="form-group">
    <div class="md:w-1/3">
      <label class="form-label @error('username') text-red-400 @enderror" for="username">Username</label>
    </div>
    <div class="md:w-2/3">
      <input required class="form-input" id="username" name="username" type="text" autofocus>
    </div>
  </div>
  <div class="form-group">
    <div class="md:w-1/3">
      <label class="form-label @error('uptoken') text-red-400 @enderror" for="uptoken">Up API token <a href="https://api.up.com.au/getting_started">(here)</a></label>
    </div>
    <div class="md:w-2/3">
      <input required class="form-input" id="uptoken" name="uptoken" type="text" autofocus>
    </div>
  </div>
  <div class="form-group">
    <div class="md:w-1/3">
      <label class="form-label @error('password') text-red-400 @enderror" for="password">Password</label>
    </div>
    <div class="md:w-2/3">
      <input required class="form-input" id="password" name="password" type="password">
    </div>
  </div>
  <div class="form-group">
    <div class="md:w-1/3">
      <label class="form-label" for="password-confirm">Confirm Password</label>
    </div>
    <div class="md:w-2/3">
      <input required class="form-input" id="password-confirm" name="password_confirmation" type="password">
    </div>
  </div>
  
  <div class="form-group">
    <div class="md:w-1/3"></div>
    <div class="md:w-2/3 text-right">
      <button class="btn-action" type="submit">Register</button>
    </div>
  </div>
</form>
@endsection
