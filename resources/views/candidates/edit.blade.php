<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold">Edit Candidate</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
            
                    <h3 class="text-lg font-bold mb-4">Edit Candidate</h3>

                    <form method="POST" action="{{ route('candidates.update', $candidate->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @include('candidates.form')

                        <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>