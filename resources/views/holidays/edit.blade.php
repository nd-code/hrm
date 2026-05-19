<x-app-layout>

    <div class="py-12">

        <div class="mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <div class="flex justify-between items-center mb-6">

                    <h3 class="text-lg font-bold">
                        Edit Holiday
                    </h3>

                    <a href="{{ route('holidays.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                        Back
                    </a>

                </div>

                <form method="POST"
                      action="{{ route('holidays.update', $holiday->id) }}">

                    @csrf

                    @include('holidays.form')

                </form>

            </div>

        </div>

    </div>

</x-app-layout>