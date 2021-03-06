@extends('layout')
@section('title', 'Login')

@section('content')

<form action="{{ route('login') }}" method="POST">
  @csrf
  <div class="form-group">
    <div class="md:w-1/3">
      <label class="form-label" for="username">Username</label>
    </div>
    <div class="md:w-2/3">
      <input required class="form-input @error('username') is-invalid @enderror" autocomplete="off" id="username" name="username" type="text">
    </div>
  </div>
  <div class="form-group">
    <div class="md:w-1/3">
      <label class="form-label" for="password">Password</label>
    </div>
    <div class="md:w-2/3">
      <input required class="form-input @error('password') is-invalid @enderror" id="password" name="password" type="password">
    </div>
  </div>
  
  <div class="form-group">
    <div class="md:w-1/3"></div>
    <div class="md:w-2/3 text-right">
      <button class="btn-action" type="submit">Login</button>
    </div>
  </div>
</form>

@endsection
