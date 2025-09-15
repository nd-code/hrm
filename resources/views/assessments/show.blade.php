<x-app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
			
				<h3 class="text-lg font-bold mb-4">Performance Assessment Detail</h3>

				<div class="mb-3">
					<strong>Employee:</strong> {{ $assessment->employee->name ?? '-' }} <br>
					<strong>Reviewer:</strong> {{ $assessment->reviewer->name ?? '-' }} <br>
					<strong>Date:</strong> {{ \Carbon\Carbon::parse($assessment->assessment_date)->format('d-m-Y') }} <br>
					<strong>Type:</strong> {{ $assessment->type ?? '-' }}
				</div>

				@include('assessments.partials.view-fields',['assessment'=>$assessment])

				<a href="{{ route('assessments.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded">Back</a>
				
			</div>
        </div>
    </div>
</x-app-layout>