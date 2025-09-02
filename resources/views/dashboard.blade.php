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
				<div class="bg-blue-500 text-white p-6 rounded-lg shadow-md">
					<h3 class="text-lg font-semibold">Employees</h3>
					<p class="text-3xl font-bold">{{ $employeeCount }}</p>
				</div>

				<!-- Reviews Count -->
				<div class="bg-green-500 text-white p-6 rounded-lg shadow-md">
					<h3 class="text-lg font-semibold">Reviews</h3>
					<p class="text-3xl font-bold">{{ $reviewCount }}</p>
				</div>

				<!-- Add more boxes as needed -->
				<div class="bg-purple-500 text-white p-6 rounded-lg shadow-md">
					<h3 class="text-lg font-semibold">Leaves</h3>
					<p class="text-3xl font-bold">{{ $leaveCount }}</p>
				</div>

				<div class="bg-red-500 text-white p-6 rounded-lg shadow-md">
					<h3 class="text-lg font-semibold">Pending Tasks</h3>
					<p class="text-3xl font-bold">0</p>
				</div>

			</div>
		</div>
	</div>
</x-app-layout>
