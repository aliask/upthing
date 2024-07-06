@extends('layout')
@section('title', 'Login')

@section('content')

@error('email')
    <span class="invalid-feedback" role="alert">
        <strong>Email: {{ $message }}</strong>
    </span>
@enderror

<form class="w-1/2" action="{{ route('login') }}" method="POST">
  @csrf
  <div class="form-group">
    <div class="md:w-1/3">
      <label class="form-label" for="email">E-mail</label>
    </div>
    <div class="md:w-2/3">
      <input required class="form-input @error('email') is-invalid @enderror" id="email" name="email" type="email">
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
