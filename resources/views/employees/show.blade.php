<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $employee->name }}'s Info
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <h3 class="text-lg font-bold mb-4">Employee Information</h3>

                <table class="w-full border-collapse border border-gray-300 mb-4">
                    <tr>
                        <th class="border p-2 text-left">Name</th>
                        <td class="border p-2">{{ $employee->name }}</td>
                    </tr>
                    <tr>
                        <th class="border p-2 text-left">Email</th>
                        <td class="border p-2">{{ $employee->email }}</td>
                    </tr>
                    <tr>
                        <th class="border p-2 text-left">Phone</th>
                        <td class="border p-2">{{ $employee->phone }}</td>
                    </tr>
                    <tr>
                        <th class="border p-2 text-left">Position</th>
                        <td class="border p-2">{{ $employee->position }}</td>
                    </tr>
                    <tr>
                        <th class="border p-2 text-left">PAN Number</th>
                        <td class="border p-2">{{ $employee->pan_number }}</td>
                    </tr>
                    <tr>
                        <th class="border p-2 text-left">Address</th>
                        <td class="border p-2">{{ $employee->address }}</td>
                    </tr>
                </table>

                <a href="{{ route('employees.index') }}" 
                   class="inline-block bg-gray-600 text-white px-4 py-2 rounded">
                   ← Back
                </a>

            </div>
        </div>
    </div>
</x-app-layout>