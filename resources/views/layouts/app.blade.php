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

    <style>
    @keyframes shake {
      0%, 100% { transform: rotate(0deg); }
      20% { transform: rotate(-15deg); }
      40% { transform: rotate(15deg); }
      60% { transform: rotate(-10deg); }
      80% { transform: rotate(10deg); }
    }

    .bell-shake {
      animation: shake 0.6s ease-in-out 2;
    }
    
    .notifCountRR{
        padding: 0px;
    }
    .notifCountRR.text-xs {
        font-size: 1.5rem;
        line-height: 1.5rem;
    }
    .notifCountRR.-top-1 {
        top: -.25rem;
    }
    .notifCountRR.-right-2 {
        right: -0.6rem;
    }
    .notifCountRR.px-1 {
        padding-right: .50rem !important;
        padding-left: .50rem !important;
    }
    </style>

</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Left Sidebar -->
        <aside class="w-64 bg-gray-800 text-white flex flex-col">
            <div class="p-4 text-2xl font-bold border-b border-gray-700">
                @if(Auth::user()->id === 101)
                    <a href="{{ route('dashboard') }}"><img src="{{ asset('images/ais.png') }}" /></a>
                @else
                    <a href="{{ route('employee.dashboard') }}"><img src="{{ asset('images/ais.png') }}" /></a>
                @endif
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
                                    <i class="fa-regular fa-comments me-2 text-yellow-400"></i> Feedback
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

                            @if(Auth::user()->position === '1' || Auth::user()->position === '2' || Auth::user()->position === '3' || Auth::user()->position === '10')
                                <li>
                                    <a href="{{ route('employee.team.index') }}"
                                       class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('employee.team.index') ? 'bg-gray-700' : '' }}">
                                        <i class="fa-solid fa-user-group me-2 text-green-400"></i> My Team
                                    </a>
                                </li>
                            @endif

                            <li>
                                <a href="{{ route('reminders.index') }}"
                                   class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('reminders.index') ? 'bg-gray-700' : '' }}">
                                    <i class="fa-solid fa-bell me-2 text-yellow-400"></i> Reminders
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </nav>

            <div class="p-4 border-t border-gray-700">
                <ul>
                    @if(Auth::user()->id === 101)
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
        <div class="flex-1 flex flex-col">
            @if(Auth::user()->id === 101)
                <!-- Send Notification Top Header -->
                <header class="bg-white shadow px-6 py-3 flex justify-end">
                    <div class="flex justify-end position-relative">
                        <div class="relative inline-block">
                            <!-- Plus Icon Trigger -->
                            <a href="javascript:void(0);"
                               class="w-10 h-10 flex items-center justify-center text-xl bg-white border rounded-full shadow hover:bg-gray-50 transition"
                               id="popupNotification">
                               <i class="fa-solid fa-plus text-blue-400"></i>
                            </a>

                            <!-- Popup -->
                            <div id="notificationPopup"
                                 class="absolute top-14 right-0 w-96 bg-white border rounded-lg shadow-lg p-4 hidden z-50">
                                <button id="closePopup" class="absolute top-2 right-2 text-red-500">✖</button>
                                <h2 class="text-lg font-semibold mb-3">Send Notification</h2>

                                <!-- Form -->
                                <form id="notificationForm" action="{{ route('admin.notifications.send') }}" method="POST">
                                    @csrf
                                    <textarea name="message"
                                              class="w-full border rounded p-2 resize-none h-32"
                                              placeholder="Type your message..." required></textarea>

                                    <div class="mt-3 flex justify-end items-center gap-2">
                                        <!-- Loader (hidden by default) -->
                                        <div id="loader" class="hidden">
                                            <svg class="animate-spin h-5 w-5 text-blue-500"
                                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                      d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                            </svg>
                                        </div>

                                        <!-- Submit Button -->
                                        <button type="submit" id="submitBtn"
                                                class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                                            Post
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>
            @else
                <!-- 🔔 Top Header -->
                <header class="bg-white shadow px-6 py-3 flex justify-end">
                    <div class="relative" style="margin-right: 40px;">
                        @php
                            $unreadCount = Auth::user()->unreadNotifications->count();
                        @endphp

                        <button id="notifBell" class="relative text-2xl">
                            @if($unreadCount > 0)
                                <i id="bellIcon" class="fa-solid fa-bell text-yellow-500" style="font-size: 40px;"></i>
                            @else
                                <i id="bellIcon" class="fa-solid fa-bell-slash text-gray-400" style="font-size: 40px;"></i>
                            @endif

                            <span id="notifCount"
                                class="notifCountRR absolute -top-1 -right-2 bg-red-500 text-white text-xs px-1 rounded-full {{ $unreadCount == 0 ? 'hidden' : '' }}">
                                {{ $unreadCount }}
                            </span>
                        </button>

                        <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white shadow rounded">
                            <ul id="notificationList" class="max-h-60 overflow-y-auto">
                                @forelse(Auth::user()->unreadNotifications as $note)
                                    <li class="px-3 py-2 border-b">
                                        {{ $note->data['message'] ?? '' }}
                                        <span class="text-gray-500 text-xs float-right">
                                            {{ $note->created_at->diffForHumans() }}
                                        </span>
                                    </li>
                                @empty
                                    <!--<li class="px-3 py-2 text-gray-500 text-sm text-center">No new notifications</li>-->
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </header>
            @endif

            <main class="flex-1 p-6">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')

    <script>
        @auth
            window.userId = {{ auth()->id() }};
        @endauth

        @if(Auth::user()->id === 101)
            const popupBtn = document.getElementById('popupNotification');
            const popup = document.getElementById('notificationPopup');
            const closeBtn = document.getElementById('closePopup');
            const loader = document.getElementById('loader');
            const submitBtn = document.getElementById('submitBtn');

            popupBtn.addEventListener('click', () => popup.classList.toggle('hidden'));
            closeBtn.addEventListener('click', () => popup.classList.add('hidden'));

            // AJAX submit with loader near Post button
            document.getElementById('notificationForm').addEventListener('submit', function(e) {
                e.preventDefault();
                let form = e.target;
                let data = new FormData(form);

                // Show loader & disable button
                loader.classList.remove('hidden');
                submitBtn.disabled = true;
                submitBtn.textContent = "Posting...";

                fetch(form.action, {
                    method: "POST",
                    headers: { 'X-CSRF-TOKEN': data.get('_token') },
                    body: data
                })
                .then(res => res.json())
                .then(resp => {
                    popup.classList.add('hidden');
                    form.reset();
                    alert("✅ Notification sent!");
                })
                .catch(err => {
                    console.error(err);
                    alert("❌ Failed to send notification. Please try again.");
                })
                .finally(() => {
                    // Hide loader & reset button
                    loader.classList.add('hidden');
                    submitBtn.disabled = false;
                    submitBtn.textContent = "Post";
                });
            });
        @else
            document.addEventListener("DOMContentLoaded", () => {
                const bell = document.getElementById('notifBell');
                const dropdown = document.getElementById('notifDropdown');
                const notifCount = document.getElementById('notifCount');

                if (bell && dropdown) {
                    bell.addEventListener('click', (e) => {
                        e.stopPropagation(); // prevent immediate close
                        dropdown.classList.toggle('hidden');

                        // 🔔 Mark as read if dropdown is now visible
                        if (!dropdown.classList.contains('hidden')) {
                            fetch("{{ route('notifications.markRead') }}", {
                                method: "POST",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                    "Accept": "application/json"
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success && notifCount) {
                                    notifCount.innerText = "0";
                                }
                            })
                            .catch(err => console.error(err));
                        }
                    });

                    // Close dropdown when clicking outside
                    document.addEventListener('click', (e) => {
                        if (!bell.contains(e.target) && !dropdown.contains(e.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });
                }
            });
        @endif
    </script>
</body>
</html>
