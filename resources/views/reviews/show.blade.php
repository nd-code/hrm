<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Review Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <h3 class="text-lg font-bold mb-4">Review Information</h3>

                <table class="w-full border-collapse border border-gray-300">
                    <tr>
                        <th class="border p-2 text-left">Employee</th>
                        <td class="border p-2">{{ $review->employee->name }}</td>
                    </tr>
                    <tr>
                        <th class="border p-2 text-left">Project</th>
                        <td class="border p-2">{{ $review->project_name }}</td>
                    </tr>
                    <tr>
                        <th class="border p-2 text-left">From</th>
                        <td class="border p-2">{{ $review->date_from }}</td>
                    </tr>
                    <tr>
                        <th class="border p-2 text-left">To</th>
                        <td class="border p-2">{{ $review->date_to }}</td>
                    </tr>
					<tr>
                        <th class="border p-2 text-left">Review Given By</th>
                        <td class="border p-2">{{ $review->review_given_by }}</td>
                    </tr>
                    <tr>
                        <th class="border p-2 text-left">Review</th>
                        <td class="border p-2">{{ $review->review }}</td>
                    </tr>
					<tr>
                        <th class="border p-2 text-left">Added At</th>
                        <td class="border p-2">{{ $review->created_at }}</td>
                    </tr>
                </table>

                <div class="mt-4">
                    <a href="{{ route('reviews.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded">Back</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>