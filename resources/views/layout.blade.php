<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <script src="https://unpkg.com/feather-icons"></script>

  <title>UpThing - @yield('title')</title>
</head>

<body>
  <nav class="navbar">
    <div class="logo">
      <svg width="80" height="70" viewBox="0 0 113 100" xmlns="http://www.w3.org/2000/svg">
        <path d="M 56.955744,0.26396 44.783739,21.11205 101.66915,78.09325 Z" />
        <path d="M 113.6824,98.31498 101.66915,78.09325 24.308662,98.31498 Z" />
        <path d="M 44.783739,21.11205 0.229088,98.31498 h 24.079574 z" />
      </svg>
      <span>UpThing</span>
    </div>

    <div class="links">

@if(Auth::user())
  <form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit" class="btn">{{Auth::user()->username}} - Sign out</button>
  </form>
@else
  @if(Route::has('register'))
    <a href="{{ route('register') }}" class="btn">Register</a>
  @endif
@endif
    </div>
  </nav>

  <div id="mainBody" class="flex">
    <div id="sidebar">
      <div id="navWrapper">
        <nav id="nav">
          <div class="section">
            <h5>Navigate</h5>
            <ul>
              <li>
                <a href="{{ route('accounts.index') }}" class="link">
                  <i data-feather="bar-chart-2"></i><span class="p-1">Accounts</span></a>
@if(isset($accounts))
                <ul>
  @foreach($accounts as $account)
                  <li class="ml-5">
                    <a href="{{ route('accounts.show', $account->id) }}" class="link">
                      {{ $account->attributes->displayName }}
                    </a>
                  </li>
  @endforeach
                </ul>
@endif
              </li>
              <li>
                <a href="{{ route('webhooks.index') }}" class="link">
                  <i data-feather="send"></i><span class="p-1">Webhooks</span>
                </a>
              </li>
            </ul>
          </div>


          <div class="section">
            <h5>Links</h5>
            <ul>
              <li>
                <a href="https://github.com/aliask/upthing" class="link">
                  <i data-feather="github"></i><span class="p-1">UpThing on GitHub</span></a>
              </li>
              <li>
                <a href="https://willrobertson.id.au/" class="link">
                  <i data-feather="feather"></i><span class="p-1">Will Robertson</span>
                </a>
              </li>
            </ul>
          </div>

        </nav>
      </div>
    </div>

    <div id="contentArea" class="">

      <h2 class="title m-b-md">
        @yield('title')
      </h2>

@foreach($errors->all() as $error)
      <div class="bg-red-100 border border-red-400 text-red-700 m-5 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">Hey!</strong>
        <span class="block sm:inline">@json($error)</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
          <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
        </span>
      </div>
@endforeach

@if(Session::has('message'))
      <div class="bg-blue-100 border border-blue-400 text-blue-700 m-5 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ Session::get('message') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
          <svg class="fill-current h-6 w-6 text-blue-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
        </span>
      </div>
@endif

      @yield('content')
    </div>

  </div> <!-- mainBody -->

  <script>
    feather.replace()
  </script>

</body>

</html>
