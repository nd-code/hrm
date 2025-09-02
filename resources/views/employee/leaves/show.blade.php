<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Leave Details
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">

                <h3 class="text-lg font-bold mb-4">Leave Information</h3>

                <table class="w-full border-collapse border border-gray-300 mb-4">
                    <tr><th class="border p-2 text-left">Leave Type</th><td class="border p-2">{{ $leave->leave_type }}</td></tr>
                    <tr><th class="border p-2 text-left">From</th><td class="border p-2">{{ \Carbon\Carbon::parse($leave->from_date)->format('d-m-Y') }}</td></tr>
                    <tr><th class="border p-2 text-left">To</th><td class="border p-2">{{ \Carbon\Carbon::parse($leave->to_date)->format('d-m-Y') }}</td></tr>
                    <tr><th class="border p-2 text-left">Reason</th><td class="border p-2">{{ $leave->reason }}</td></tr>
                    <tr><th class="border p-2 text-left">Status</th><td class="border p-2">{{ $leave->status }}</td></tr>
                </table>

                <a href="{{ route('employee.leaves.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded">← Back</a>

            </div>
        </div>
    </div>
</x-app-layout>