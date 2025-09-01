<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex">
			<!-- Left Sidebar -->
			<aside class="w-64 bg-gray-800 text-white flex flex-col">
				<div class="p-4 text-2xl font-bold border-b border-gray-700">
					@auth
						@if(Auth::user()->id === 1)
							<a href="/">
								<img src="{{ asset('images/ais.png') }}" />
							</a>
						@else
							<a href="/dashboard">
								<img src="{{ asset('images/ais.png') }}" />
							</a>
						@endif
					@else
						<a href="/">
							<img src="{{ asset('images/ais.png') }}" />
						</a>
					@endauth
				</div>

				<nav class="flex-1 p-4">
					<ul class="space-y-2">
						@auth
							@if(Auth::user()->id === 1)
								<li>
									<a href="{{ route('dashboard') }}"
									   class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-gray-700' : '' }}">
										Dashboard
									</a>
								</li>
								<li>
									<a href="{{ route('employees.index') }}"
									   class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('employees.*') ? 'bg-gray-700' : '' }}">
										Employees List
									</a>
								</li>
								<li>
									<a href="{{ route('reviews.index') }}"
									   class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('reviews.*') ? 'bg-gray-700' : '' }}">
										Feedback Management
									</a>
								</li>
								<li>
									<a href="{{ route('leaves.index') }}"
									   class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('leaves.*') ? 'bg-gray-700' : '' }}">
										Leave Management
									</a>
								</li>
							@else
								<li>
									<a href="{{ route('employee.dashboard') }}"
									   class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('employee.dashboard') ? 'bg-gray-700' : '' }}">
										My Dashboard
									</a>
								</li>
							@endif
						@endauth
					</ul>
				</nav>

				<div class="p-4 border-t border-gray-700">
					<form method="POST" action="{{ route('logout') }}">
						@csrf
						<button class="w-full text-left px-4 py-2 rounded hover:bg-gray-700">
							Logout
						</button>
					</form>
				</div>
			</aside>

			<!-- Main Content -->
			<main class="flex-1 p-6">
				{{ $slot ?? '' }}
				@yield('content')
			</main>
		</div>
    </body>
</html>
