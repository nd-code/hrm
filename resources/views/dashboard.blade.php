<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <!-- Employees Count -->
                <a href="{{ route('employees.index') }}">
                    <div class="bg-blue-500 text-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold">Employees</h3>
                        <p class="text-3xl font-bold">{{ $employeeCount }}</p>
                    </div>
                </a>

                <!-- Reviews Count -->
                <a href="{{ route('reviews.index') }}">
                    <div class="bg-green-500 text-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold">Feedback</h3>
                        <p class="text-3xl font-bold">{{ $reviewCount }}</p>
                    </div>
                </a>

                <!-- Leaves Count -->
                <a href="{{ route('leaves.index') }}">
                    <div class="bg-purple-500 text-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold">Leaves</h3>
                        <p class="text-3xl font-bold">{{ $leaveCount }}</p>
                    </div>
                </a>

                <!-- KPA Count -->
                <a href="{{ route('assessments.index') }}">
                    <div class="bg-red-500 text-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold">KPA</h3>
                        <p class="text-3xl font-bold">{{ $assessmentCount }}</p>
                    </div>
                </a>

            </div>

            <!-- Online Employees & Notifications -->
            <div class="w-full flex gap-6 mt-8">
                {{-- ============================= --}}
                {{-- Online Employees Section --}}
                {{-- ============================= --}}
                
                <div class="w-1/2">
                    <h3 class="text-lg font-semibold mb-3">🟢 Online Employees</h3>
                    <table class="w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 border">Employee</th>
                                <th class="px-4 py-2 border">Date</th>
                                <th class="px-4 py-2 border">Start Time</th>
                            </tr>
                        </thead>
                        <tbody id="online-employees-body">
                            @include('online-employees', ['onlineEmployees' => $onlineEmployees])
                        </tbody>
                    </table>
                </div>
                
                {{-- ============================= --}}
                {{-- Notifications Section --}}
                {{-- ============================= --}}

                <div class="w-1/2">
                    <h3 class="text-lg font-semibold mb-3">🕒 Employees on Leave Today</h3>
                    <table class="w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 border">Employee Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($employeesOnLeave->isEmpty())
                                <tr><td class="px-4 py-2 border">No employees are on leave today.</td></tr>
                            @else
                                @foreach($employeesOnLeave as $leave)
                                    <tr>
                                        <td class="px-4 py-2 border"><a style="text-decoration: underline;" href="{{ route('employees.show', $leave->employee->id) }}">{{ $leave->employee->name }}</a></td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    
                    <h3 class="text-lg font-semibold mt-4 mb-3">📌 Notifications</h3>
                    @if($notifications->count())
                        <ul class="space-y-2">
                            @foreach ($notifications as $note)
                                @php
                                    $data = json_decode($note->data, true);
                                @endphp
                                <li class="flex items-center gap-2 p-2 border rounded bg-gray-50">
                                    <span class="text-gray-800 font-medium">
                                        {{ $data['message'] ?? '' }}
                                    </span>
                                    <span class="text-xs text-gray-500 ml-auto">
                                        {{ \Carbon\Carbon::parse($note->latest_created_at ?? $note->created_at)->diffForHumans() }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-600">No notification 🎉</p>
                    @endif
                </div>
            </div>
            
        </div>
    </div>

    <script>
       function loadOnlineEmployees() {
            fetch('dashboard/online')
                .then(response => response.text())
                .then(html => {
                    const tbody = document.getElementById('online-employees-body');
                    if (tbody) {
                        tbody.innerHTML = html;
                    }
                })
                .catch(error => console.error('Error fetching employees:', error));
        }

    // Refresh every 5 seconds
        setInterval(loadOnlineEmployees, 5000);
    </script>
</x-app-layout>