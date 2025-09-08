@php
	use App\Models\WorkSession;
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
					
					@if(session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @php
                        $session = WorkSession::where('employee_id', Auth::id())
                            ->whereDate('work_date', today())
                            ->latest()
                            ->first();
                    @endphp

                    @if($session && !$session->end_time)
                        <div class="p-4 bg-green-100 text-green-800 rounded mb-4">
                            You are <strong>working</strong>
                            (Started at: {{ $session->start_time->format('H:i A') }})
                        </div>
                    @elseif($session && $session->end_time)
                        <div class="p-4 bg-red-100 text-red-800 rounded mb-4">
                            Last work session ended at: {{ $session->end_time->format('H:i A') }}
                        </div>
                    @else
                        <div class="p-4 bg-gray-100 text-gray-800 rounded mb-4">
                            You haven't started working yet today.
                        </div>
                    @endif
					
					@php
						$todaySession = WorkSession::where('employee_id', Auth::id())
							->whereDate('work_date', today())
							->latest()
							->first();
					@endphp

					<form method="POST" action="{{ route('employee.work.timer') }}">
						@csrf
						@if($todaySession && !$todaySession->end_time)
							<button class="block text-left px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">
								Stop Work
							</button>
						@else
							<button class="block text-left px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">
								Start Work
							</button>
						@endif
					</form>
				</div>
            </div>
        </div>
    </div>
</x-app-layout>