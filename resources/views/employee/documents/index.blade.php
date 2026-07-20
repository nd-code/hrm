<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">
            My Documents
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-6">

            @if($documents->count())

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

                    @foreach($documents as $doc)

                        @php
                            $ext = strtolower(pathinfo($doc->file_name, PATHINFO_EXTENSION));
                        @endphp

                        <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">

                            {{-- Preview --}}
                            <div class="h-44 bg-gray-100 flex items-center justify-center">

                                @if(in_array($ext,['jpg','jpeg','png','gif','webp']))
                                    <img src="{{ asset('storage/'.$doc->file_path) }}"
                                         class="w-full h-full object-cover">
                                @elseif($ext=='pdf')
                                    <img src="{{ asset('images/pdf.png') }}"
                                         class="w-20">
                                @elseif(in_array($ext,['doc','docx']))
                                    <img src="{{ asset('images/word.png') }}"
                                         class="w-20">
                                @else
                                    <img src="{{ asset('images/file.png') }}"
                                         class="w-20">
                                @endif

                            </div>

                            {{-- Details --}}
                            <div class="p-4">

                                <h3 class="font-semibold truncate">
                                    {{ $doc->file_name }}
                                </h3>

                                <p class="text-gray-500 text-sm mt-1">
                                    {{ $doc->created_at->format('d M Y') }}
                                </p>

                                <div class="flex justify-between mt-4">

                                    <a href="{{ asset('storage/'.$doc->file_path) }}"
                                       target="_blank"
                                       class="text-blue-600 font-medium">
                                        View
                                    </a>

                                    <a href="{{ asset('storage/'.$doc->file_path) }}"
                                       download="{{ $doc->file_name }}"
                                       class="text-green-600 font-medium">
                                        Download
                                    </a>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="bg-white rounded-lg p-10 text-center text-gray-500">
                    No documents uploaded.
                </div>

            @endif

        </div>
    </div>

</x-app-layout>