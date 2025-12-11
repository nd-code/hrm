<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold">Add Candidate</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
            
                    <h3 class="text-lg font-bold mb-4">Add Candidate</h3>

                    <form method="POST" action="{{ route('candidates.store') }}" enctype="multipart/form-data">
                        @csrf

                        @include('candidates.form')

                        <button class="bg-green-600 text-white px-4 py-2 rounded">Save</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>