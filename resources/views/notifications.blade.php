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

                    @if (session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

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
                                        {{ \Carbon\Carbon::parse($note->created_at)->diffForHumans() }}
                                    </span>

                                    <form action="{{ route('notifications.deleteByData') }}" method="POST" onsubmit="return confirm('Are you sure want to clear this notification?');">
                                        @csrf
                                        <input type="hidden" name="message" value="{{ $data['message'] }}">
                                        <button type="submit" class="text-red-500 hover:text-red-700 ml-2" title="Delete notification">Clear</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-600">No notification 🎉</p>
                    @endif
                    
                    <br>
                    <a href="{{ route('setting') }}" 
                       class="inline-block bg-gray-600 text-white px-4 py-2 rounded">
                       ← Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>