<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="{{ asset('css/tw.css') }}">

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
      <div class="text-sm lg:flex-grow">
        <a href="{{ route('accounts.index') }}" class="block mt-4 lg:inline-block lg:mt-0 text-orange-200 hover:text-white mr-4">
          Accounts
        </a>
        <a href="{{ route('webhooks.index') }}" class="block mt-4 lg:inline-block lg:mt-0 text-orange-200 hover:text-white mr-4">
          Webhooks
        </a>
      </div>

      <div>
        <a href="#" class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-orange-500 hover:bg-white mt-4 lg:mt-0">Download</a>
      </div>
    </div>
  </nav>

  <div id="mainBody" class="lg:flex">
    <div id="sidebar" class="fixed inset-0 h-full bg-white z-90 w-full border-b -mb-16 lg:-mb-0 lg:static lg:h-auto lg:overflow-y-visible lg:border-b-0 lg:pt-0 lg:w-1/4 lg:block lg:border-0 xl:w-1/5 hidden pt-16">
      <div id="navWrapper" class="h-full overflow-y-auto scrolling-touch lg:h-auto lg:block lg:relative lg:sticky lg:bg-transparent overflow-hidden lg:top-16 bg-white">
        <nav id="nav" class="px-6 pt-6 overflow-y-auto text-base lg:text-sm lg:py-12 lg:pl-6 lg:pr-8 sticky?lg:h-(screen-16)">
          <div class="relative -mx-2 w-24 mb-8 lg:hidden">
            <form><label><span class="sr-only">Tailwind CSS Version</span><select class="appearance-none block bg-transparent pl-2 pr-8 py-1 text-gray-500 font-medium text-base focus:outline-none focus:text-gray-800">
                  <option value="v1">v
                    <!-- -->1.6.2</option>
                  <option value="v0">v0.7.4</option>
                </select></label></form>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"><svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"></path>
              </svg></div>
          </div>

          <div class="mb-8">
            <h5 class="mb-3 lg:mb-2 uppercase tracking-wide font-bold text-sm lg:text-xs text-gray-500">Navigate</h5>
            <ul>
              <li class="mb-3 lg:mb-1">
                <a href="{{ route('accounts.index') }}" class="px-2 -mx-2 py-1 transition duration-200 ease-in-out relative block hover:translate-x-2px hover:text-gray-900 text-gray-600 font-medium">
                  <span class="relative">Accounts</span></a>
@if(isset($accounts))
                <ul>
  @foreach($accounts as $account)
                  <li class="mb-3 lg:mb-1 ml-5">
                    <a href="{{ route('accounts.show', $account->id) }}" class="px-2 -mx-2 py-1 transition duration-200 ease-in-out relative block hover:translate-x-2px hover:text-gray-900 text-gray-600 font-medium">
                      {{ $account->attributes->displayName }}
                    </a>
                  </li>
  @endforeach
                </ul>
@endif
              </li>
              <li class="mb-3 lg:mb-1">
                <a href="{{ route('webhooks.index') }}" class="px-2 -mx-2 py-1 transition duration-200 ease-in-out relative block hover:translate-x-2px hover:text-gray-900 text-gray-600 font-medium">
                  <span class="relative">Webhooks</span>
                </a>
              </li>
            </ul>
          </div>
        </nav>
      </div>
    </div>

    <div class="min-h-screen w-full lg:static lg:max-h-full lg:overflow-visible lg:w-3/4 xl:w-4/5  lg:py-12">

      <h2 class="title m-b-md">
        @yield('title')
      </h2>

@if(Session::has('error'))
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">Holy smokes!</strong>
        <span class="block sm:inline">{{ Session::get('error') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
          <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
        </span>
      </div>
@endif

      @yield('content')
    </div>

  </div> <!-- mainBody -->

</body>

</html>
