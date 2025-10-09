@php
    use App\Models\WorkSession;
    use App\Models\Reminder;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employee Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
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

                        <div class="w-1/2">
                            <h3 class="text-lg font-semibold mb-3">📝 Reminders</h3>

                            @if($todayReminders->count())
                                <table class="w-full border">
                                    <thead>
                                        <tr>
                                            <th class="border px-2 py-1">Date</th>
                                            <th class="border px-2 py-1">Subject</th>
                                            <th class="border px-2 py-1">Description</th>
                                            <th class="border px-2 py-1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($todayReminders as $reminder)
                                            <tr>
                                                <td class="border px-2 py-1">{{ \Carbon\Carbon::parse($reminder->date)->format('d-m-Y') }}</td>
                                                <td class="border px-2 py-1">{{ $reminder->subject }}</td>
                                                <td class="border px-2 py-1">{{ $reminder->description }}</td>
                                                <td class="border px-2 py-1">
                                                    <form action="{{ route('employee.reminders.complete', $reminder->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded">
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

                        <div class="w-1/2">
                            <h3 class="text-lg font-semibold mb-3">📌 Notifications</h3>
                            @if($latestNotifications->count())
                                <ul class="space-y-2">
                                    @foreach ($latestNotifications as $note)
                                        <li class="flex items-center gap-2 p-2 border rounded bg-gray-50">
                                            <span class="blinking text-gray-800 font-medium">
                                                {{ $note->data['message'] ?? '' }}
                                            </span>
                                            <span class="text-xs text-gray-500 ml-auto">
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
<style>
@keyframes blink {
    50% { opacity: 0; }
}
.blinking {
    animation: blink 1s step-start infinite;
}
</style>
</x-app-layout>