<x-app-layout>

    <div class="py-12">

        <div class="mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <div class="flex justify-between items-center mb-6">

                    <h3 class="text-lg font-bold">
                        Holiday List
                    </h3>

                </div>

                <div class="overflow-x-auto">

                    <table class="min-w-full border border-gray-300">

                        <thead class="bg-gray-100">

                            <tr>

                                <th class="border px-4 py-3 text-left">
                                    #
                                </th>

                                <th class="border px-4 py-3 text-left">
                                    Holiday
                                </th>

                                <th class="border px-4 py-3 text-left">
                                    Date
                                </th>
                                
                                <th class="border px-4 py-3 text-left">
                                    Optional
                                </th>

                                <th class="border px-4 py-3 text-left">
                                    Description
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($holidays as $holiday)

                                <tr>

                                    <td class="border px-4 py-3">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="border px-4 py-3">
                                        {{ $holiday->title }}
                                    </td>

                                    <td class="border px-4 py-3">
                                        {{ \Carbon\Carbon::parse($holiday->holiday_date)->format('d M Y') }}
                                    </td>
                                    
                                    <td class="border px-4 py-3">
                                        {{ $holiday->is_optional ? 'Yes' : 'No' }}
                                    </td>

                                    <td class="border px-4 py-3">
                                        {{ $holiday->description ?? '-' }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="4"
                                        class="border px-4 py-6 text-center text-gray-500">
                                        No holidays available.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>