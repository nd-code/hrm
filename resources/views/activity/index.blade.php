<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Activity Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h3 class="text-lg font-bold mb-4">Activity Logs</h3>

                    {{-- Empty state OUTSIDE table (DataTables safe) --}}
                    @if($logs->isEmpty())
                        <div class="text-center py-6 text-gray-500">
                            No activity log found.
                        </div>
                    @else
                        <table id="activityTable" class="display w-full mt-4">
                            <thead>
                                <tr>
                                    <th style="display: none;">ID</th>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Module</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($logs as $log)
                                    <tr>
                                        <td style="display: none;">{{ $log->id }}</td>
                                        <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>{{ optional($log->user)->name }}</td>
                                        <td>{{ $log->module }}</td>
                                        <td>{{ $log->action }}</td>
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
        #activityTable tr.odd { background-color: #f9fafb; }
        #activityTable tr.even { background-color: #ffffff; }
    </style>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    
    <script>
    $(document).ready(function () {
        $('#activityTable').DataTable({
            pageLength: 10,
            order: [[0, 'desc']],
            searching: true, // 🔍 ENABLED
            orderClasses: false, // 🔥 disables sorting_1 class
        });
    });
    </script>

</x-app-layout>