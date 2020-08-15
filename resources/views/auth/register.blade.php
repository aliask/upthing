@extends('layout')
@section('title', 'Register')

@section('content')

@if($errors->all())
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">Holy smokes!</strong>
        <span class="block sm:inline">@json($errors->all())</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
          <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
        </span>
      </div>
@endif


<form class="w-1/2" action="{{ route('register') }}" method="POST">
  @csrf
  <div class="form-group">
    <div class="md:w-1/3">
      <label class="form-label @error('name') text-red-400 @enderror" for="name">Username</label>
    </div>
    <div class="md:w-2/3">
      <input required class="form-input" id="name" name="name" type="text" autofocus>
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
