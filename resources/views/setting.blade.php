<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Setting') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="{{ route('vendors.index') }}">
                    <div class="p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold">Vendors</h3>
                    </div>
                </a>

                <a href="#">
                    <div class="p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold">Candidate List</h3>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>