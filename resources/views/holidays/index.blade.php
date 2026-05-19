<x-app-layout>

    <div class="py-12">

        <div class="mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <div class="flex justify-between items-center mb-6">

                    <h3 class="text-lg font-bold">
                        Holiday List
                    </h3>

                    <a href="{{ route('holidays.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Add Holiday
                    </a>

                </div>

                @if(session('success'))

                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>

                @endif

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
                                    Action
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

                                        <div class="flex items-center gap-2">

                                            <a href="{{ route('holidays.edit', $holiday->id) }}"
                                               class="inline-flex items-center px-3 py-1 bg-yellow-500 border border-transparent rounded text-xs text-white hover:bg-yellow-600">
                                                Edit
                                            </a>

                                            <form method="POST"
                                                  action="{{ route('holidays.delete', $holiday->id) }}"
                                                  onsubmit="return confirm('Delete holiday?')">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded text-xs text-white hover:bg-red-700">
                                                    Delete
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5"
                                        class="border px-4 py-6 text-center text-gray-500">
                                        No holidays found.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-4">
                    {{ $holidays->links() }}
                </div>

            </div>

        </div>

    </div>

</x-app-layout>