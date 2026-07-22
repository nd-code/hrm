<x-app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h3 class="text-lg font-bold mb-4">My Documents</h3>

                    <div class="py-8">
                        <div class="max-w-8xl mx-auto px-6">
                            <div class="flex justify-end items-center mb-6">
                                <div class="w-full md:w-96">
                                    <input
                                        type="text"
                                        id="document-search"
                                        placeholder="Search document..."
                                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    >
                                </div>
                            </div>

                            @if($documents->count())

                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

                                    @foreach($documents as $doc)

                                        @php
                                            $ext = strtolower(pathinfo($doc->file_name, PATHINFO_EXTENSION));
                                        @endphp

                                        <div class="document-card bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden" data-name="{{ strtolower($doc->file_name) }}">

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
                            
                                <div id="no-documents-found"
                                    class="hidden bg-white rounded-lg p-10 text-center text-gray-500 mt-6">
                                   <h3 class="text-lg font-semibold">No document found.</h3>
                                   <p class="text-sm text-gray-400 mt-2">
                                       Try searching with a different file name.
                                   </p>
                               </div>

                            @else

                                <div class="bg-white rounded-lg p-10 text-center text-gray-500">
                                    No documents uploaded.
                                </div>

                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const searchInput = document.getElementById('document-search');
            const cards = document.querySelectorAll('.document-card');
            const noResult = document.getElementById('no-documents-found');

            searchInput.addEventListener('keyup', function () {

                let keyword = this.value.toLowerCase().trim();
                let visibleCount = 0;

                cards.forEach(function(card){

                    let name = card.dataset.name;

                    if(name.includes(keyword)){
                        card.style.display = '';
                        visibleCount++;
                    }else{
                        card.style.display = 'none';
                    }

                });

                if(visibleCount === 0){
                    noResult.classList.remove('hidden');
                }else{
                    noResult.classList.add('hidden');
                }

            });

        });
    </script>
</x-app-layout>