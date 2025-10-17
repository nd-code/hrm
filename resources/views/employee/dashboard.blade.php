@php
    use App\Models\WorkSession;
    use App\Models\Reminder;
    use App\Models\Leave;
    use App\Models\Team;

    $teamEmployees = Team::with('employee')
        ->where('parent_employee_id', auth('employee')->id())
        ->get();

    $existingIds = $teamEmployees->pluck('employee_id')->toArray();

    // Get team members currently on leave today
    $today = \Carbon\Carbon::today();

    $teamLeaves = Leave::with('employee')
        ->whereIn('employee_id', $existingIds)
        ->where('from_date', '<=', $today)
        ->where('to_date', '>=', $today)
        ->where('status', 'Approved')
        ->orderBy('id', 'desc')
        ->get();

    // Get online team members (from work_sessions table)
    $onlineTeamMembers = WorkSession::with('employee')
        ->whereIn('employee_id', $existingIds)
        ->whereNull('end_time') // means still working
        ->latest()
        ->get();
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employee Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto sm:px-1 lg:px-1">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h2 class="mb-4">Welcome {{ auth()->guard('employee')->user()->name }}</h2>

                    {{-- Flash success message --}}
                    @if(session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Work session status --}}
                    @php
                        // First check if there's any open session (even from yesterday)
                        $openSession = WorkSession::where('employee_id', Auth::id())
                            ->whereNull('end_time')
                            ->latest()
                            ->first();

                        // Then check today's latest session (your original logic)
                        $todaySession = WorkSession::where('employee_id', Auth::id())
                            ->whereDate('work_date', today())
                            ->latest()
                            ->first();
                    @endphp

                    @if($openSession)
                        <div class="p-4 bg-green-100 text-green-800 rounded mb-4">
                            You are <strong>working</strong>
                            (Started at: {{ $openSession->start_time->format('H:i A, d M Y') }})
                        </div>
                    @elseif($todaySession && $todaySession->end_time)
                        <div class="p-4 bg-red-100 text-red-800 rounded mb-4">
                            Last work session ended at: {{ $todaySession->end_time->format('H:i A') }}
                        </div>
                    @else
                        <div class="p-4 bg-gray-100 text-gray-800 rounded mb-4">
                            You haven't started working yet today.
                        </div>
                    @endif


                    {{-- Work session toggle button --}}
                    <form method="POST" action="{{ route('employee.work.timer') }}">
                        @csrf
                        @if($openSession)
                            <textarea id="comment" name="comment" placeholder="Enter your work details here...." class="mb-2" style="width: 300px;"></textarea>
                            <button class="block text-left px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                                Offline
                            </button>
                        @else
                            <button class="block text-left px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">
                                Online
                            </button>
                        @endif
                    </form>

                    @if(Auth::user()->position === '1' || Auth::user()->position === '2' || Auth::user()->position === '3' || Auth::user()->position === '10')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                            <!-- Online Employees -->
                            <div>
                                <h3 class="text-lg font-semibold mb-3">🟢 Online Team Members</h3>
                                <table class="w-full border">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="px-4 py-2 border">Employee</th>
                                            <th class="px-4 py-2 border">Date</th>
                                            <th class="px-4 py-2 border">Start Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($onlineTeamMembers as $session)
                                            <tr>
                                                <td class="px-4 py-2 border">{{ $session->employee->name }}</td>
                                                <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($session->start_time)->format('d-m-Y') }}</td>
                                                <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($session->start_time)->format('h:i A') }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="2" class="px-4 py-2 border">No team members online.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Employees on Leave -->
                            <div>
                                <h3 class="text-lg font-semibold mb-3">🕒 Team Members on Leave Today</h3>
                                <table class="w-full border">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="px-4 py-2 border">Employee</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($teamLeaves as $leave)
                                            <tr>
                                                <td class="px-4 py-2 border">{{ $leave->employee->name }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="2" class="px-4 py-2 border">No team members on leave today.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- ============================= --}}
                    {{-- Reminders + Notifications Section --}}
                    {{-- ============================= --}}

                    <div class="flex gap-6 mt-8">
                        {{-- ============================= --}}
                        {{-- Reminders Section --}}
                        {{-- ============================= --}}

                        @php
                            $todayReminders = Reminder::where('employee_id', auth('employee')->id())
                                ->where('status', 'Pending')
                                ->whereDate('date', '<=', today()) // due today or overdue
                                ->orderBy('date', 'asc')
                                ->get();
                        @endphp

                        <div class="w-1/2 p-3 mb-0 text-sm text-blue-800 border border-blue-300 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400 dark:border-blue-800">
                            <h3 class="text-lg font-semibold mb-3">📝 Reminders</h3>

                            @if($todayReminders->count())
                                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th class="border px-2 py-1">Date</th>
                                            <th class="border px-2 py-1">Subject</th>
                                            <th class="border px-2 py-1">Description</th>
                                            <th class="border px-2 py-1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($todayReminders as $reminder)
                                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                                <td class="border px-2 py-1">{{ \Carbon\Carbon::parse($reminder->date)->format('d-m-Y') }}</td>
                                                <td class="border px-2 py-1">{{ $reminder->subject }}</td>
                                                <td class="border px-2 py-1">{{ $reminder->description }}</td>
                                                <td class="border px-2 py-1">
                                                    <form action="{{ route('employee.reminders.complete', $reminder->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="px-2 py-1 text-xs font-medium text-center text-white bg-red-700 rounded-lg hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                                                            Clear
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-gray-600">No reminder 🎉</p>
                            @endif
                        </div>

                        {{-- ============================= --}}
                        {{-- Notifications Section --}}
                        {{-- ============================= --}}

                        @php
                            $latestNotifications = auth('employee')->user()
                                ->notifications()
                                ->latest()
                                ->take(5)
                                ->get();
                        @endphp

                        <div class="w-1/2 space-y-2 p-3 mb-0 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800">
                            <h3 class="text-lg font-semibold mb-3">📌 Notifications</h3>
                            @if($latestNotifications->count())
                                <ul class="">
                                    @foreach ($latestNotifications as $note)
                                        <li class="p-3 mb-0 text-sm text-black border border-red-300  bg-white dark:text-blue-400 notificationBox">
                                            <span class="text-gray-800 font-medium">
                                                {{ $note->data['message'] ?? '' }}
                                            </span> <br>
                                            <span class="text-xs text-gray-500 ml-auto timeNotification ">
                                                {{ $note->created_at->diffForHumans() }}
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
        </div>
    </div>
</x-app-layout>

<style>
    .notificationBox {
        position: relative;
        border:0 !important;
        border:1px solid #f1d6d6 !important;
    }
    .notificationBox:first-child {
        border-bottom:0 !important;
    }
    .timeNotification {
        background: #f5d2d2;
        padding: 1px 8px;
        border-radius: 15px;
        border: 1px solid #f5d2d2;
        margin-top: 5px;
        display: inline-block;
        font-size: 11px !important;
        color: #9c2f2f;
        font-weight:500;
    }
</style>
