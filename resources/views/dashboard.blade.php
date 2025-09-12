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
		</div>
	</div>
</x-app-layout>
