<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
		<div class="mx-auto sm:px-6 lg:px-8">
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
			
			<!-- Online Employees -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mt-6">
                <h3 class="text-lg font-semibold mb-4">Today's Online Employees</h3>

                @if($onlineEmployees->count() > 0)
					<table class="w-full border">
						<thead>
							<tr class="bg-gray-100">
								<th class="px-4 py-2 border">Employee</th>
								<th class="px-4 py-2 border">Date</th>
								<th class="px-4 py-2 border">Start Time</th>
								<th class="px-4 py-2 border">End Time</th>
								<th class="px-4 py-2 border">Total Hours</th>
							</tr>
						</thead>
						<tbody>
							@foreach($onlineEmployees as $session)
								@php
									$start = $session->start_time ? \Carbon\Carbon::parse($session->start_time) : null;
									$end = $session->end_time ? \Carbon\Carbon::parse($session->end_time) : null;
									$total = ($start && $end) ? $start->diff($end)->format('%H:%I:%S') : '-';
								@endphp
								<tr>
									<td class="px-4 py-2 border">{{ $session->employee->name }}</td>
									<td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($session->work_date)->format('d-m-Y') }}</td>
									
									{{-- Convert to 12-hour format --}}
									<td class="px-4 py-2 border">
										{{ $start ? $start->format('h:i A') : '-' }}
									</td>
									<td class="px-4 py-2 border">
										{{ $end ? $end->format('h:i A') : '-' }}
									</td>
									
									<td class="px-4 py-2 border">{{ $total }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				@else
					<p>No employees are online right now.</p>
				@endif
            </div>
		</div>
	</div>
</x-app-layout>
