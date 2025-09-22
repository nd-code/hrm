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

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

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
                                            @if(Auth::user()->id === 101)
                                                <!-- Admin Menu -->
                                                <li>
                                                    <a href="{{ route('dashboard') }}"
                                                       class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-gray-700' : '' }}">
                                                        <i class="fa-solid fa-gauge me-2 text-blue-400"></i> Dashboard
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="{{ route('employees.index') }}"
                                                       class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('employees.*') ? 'bg-gray-700' : '' }}">
                                                        <i class="fa-solid fa-users me-2 text-green-400"></i> Employees List
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="{{ route('reviews.index') }}"
                                                       class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('reviews.*') ? 'bg-gray-700' : '' }}">
                                                        <i class="fa-regular fa-comments me-2 text-yellow-400"></i> Feedback Management
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="{{ route('leaves.index') }}"
                                                       class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('leaves.*') ? 'bg-gray-700' : '' }}">
                                                        <i class="fa-regular fa-calendar-days me-2 text-purple-400"></i> Leave Management
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="{{ route('assessments.index') }}"
                                                       class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('assessments.*') ? 'bg-gray-700' : '' }}">
                                                        <i class="fa-solid fa-chart-line me-2 text-red-400"></i> KPA
                                                    </a>
                                                </li>
                                            @else
                                                <!-- Employee Menu -->
                                                <li>
                                                    <a href="{{ route('employee.dashboard') }}"
                                                       class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('employee.dashboard') ? 'bg-gray-700' : '' }}">
                                                        <i class="fa-solid fa-house-user me-2 text-cyan-400"></i> My Dashboard
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="{{ route('employee.leaves.index') }}"
                                                       class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('employee.leaves.index') ? 'bg-gray-700' : '' }}">
                                                        <i class="fa-regular fa-calendar-check me-2 text-pink-400"></i> Leaves
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="{{ route('employee.work.index') }}"
                                                       class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('employee.work.index') ? 'bg-gray-700' : '' }}">
                                                        <i class="fa-solid fa-briefcase me-2 text-orange-400"></i> My Work
                                                    </a>
                                                </li>
                                            @endif
                                        @endauth
                                    </ul>
                                </nav>

                                <div class="p-4 border-t border-gray-700">
                                    <ul>
                                        @if(Auth::user()->id == 101)
                                            <li>
                                                <a href="{{ route('setting') }}"
                                                   class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('employee.profile') ? 'bg-gray-700' : '' }}">
                                                    <i class="fa-solid fa-gear me-2 text-gray-400"></i> Setting
                                                </a>
                                            </li>
                                        @else
                                            <li>
                                                <a href="{{ route('employee.profile') }}"
                                                   class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('employee.profile') ? 'bg-gray-700' : '' }}">
                                                    <i class="fa-regular fa-id-card me-2 text-indigo-400"></i> My Details
                                                </a>
                                            </li>
                                        @endif

                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button class="w-full text-left px-4 py-2 rounded hover:bg-gray-700">
                                                    <i class="fa-solid fa-right-from-bracket me-2 text-red-500"></i> Logout
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
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
