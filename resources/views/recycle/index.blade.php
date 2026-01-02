<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Recycle Bin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h3 class="text-lg font-bold mb-4">Recycle Bin</h3>

                    {{-- Empty state OUTSIDE table (DataTables safe) --}}
                    @if($items->isEmpty())
                        <div class="text-center py-6 text-gray-500">
                            No deleted records found.
                        </div>
                    @else
                        <table id="recycleTable" class="display w-full mt-4">
                            <thead>
                                <tr>
                                    <th style="display: none;">ID</th>
                                    <th>Module</th>
                                    <th>Record ID</th>
                                    <th>Deleted Data</th>
                                    <th>Deleted At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td style="display: none;">{{ $item->id }}</td>
                                        <td>{{ ucfirst($item->module) }}</td>
                                        <td>{{ $item->record_id }}</td>
                                        <td data-search="{{ implode(' ', $item->data) }}">
                                            <pre class="text-xs max-h-40 overflow-auto">
                                                {{ json_encode($item->data, JSON_PRETTY_PRINT) }}
                                            </pre>
                                        </td>
                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="flex items-center gap-3">

                                                {{-- Restore --}}
                                                <form method="POST" action="{{ route('recycle.restore', $item->id) }}">
                                                    @csrf
                                                    <button
                                                        type="submit"
                                                        title="Restore"
                                                        class="text-green-600 hover:text-green-800"
                                                    >
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>

                                                {{-- Delete --}}
                                                <form method="POST" action="{{ route('recycle.delete', $item->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        title="Delete Permanently"
                                                        class="text-red-600 hover:text-red-800"
                                                        onclick="return confirm('Are you sure you want to permanently delete this record?')"
                                                    >
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                </div>
            </div>
        </div>
    </div>
    
    <!-- Styles -->
    <style>
        #recycleTable tr.odd { background-color: #f9fafb; }
        #recycleTable tr.even { background-color: #ffffff; }
    </style>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    
    <script>
    $(document).ready(function () {
        $('#recycleTable').DataTable({
            pageLength: 10,
            order: [[0, 'desc']],
            searching: true, // 🔍 ENABLED
            orderClasses: false, // 🔥 disables sorting_1 class
            columnDefs: [
                { orderable: false, targets: [5] } // Disable sort on Action
            ]
        });
    });
    </script>

</x-app-layout>