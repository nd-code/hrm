<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Setting') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Notifications</h3>
                    @if($notifications->count())
                        <ul class="space-y-2">
                            @foreach ($notifications as $note)
                                @php
                                    $data = json_decode($note->data, true);
                                @endphp
                                <li class="flex items-center gap-2 p-2 border rounded bg-gray-50">
                                    <span class="text-gray-800 font-medium">
                                        {{ $data['message'] ?? '' }}
                                    </span>
                                    <span class="text-xs text-gray-500 ml-auto">
                                        {{ \Carbon\Carbon::parse($note->latest_created_at ?? $note->created_at)->diffForHumans() }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-600">No notification 🎉</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>