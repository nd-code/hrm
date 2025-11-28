<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Work') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                
                <h3 class="text-lg font-bold mb-4">My Work</h3>
                
                <!-- Attendance Filters -->
                <div class="mb-4 flex items-end gap-4">

                    <form method="GET" action="{{ route('employee.work.index') }}" class="mb-4 flex items-end gap-4">

                        <div>
                            <label class="block text-sm font-medium">From Date</label>
                            <input type="date" name="from_date" value="{{ request('from_date') }}" class="border p-2 rounded w-40">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">To Date</label>
                            <input type="date" name="to_date" value="{{ request('to_date') }}" class="border p-2 rounded w-40">
                        </div>

                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                            Filter
                        </button>

                        <a href="{{ route('employee.work.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded">
                            Reset
                        </a>

                        <a id="att_export_pdf_btn"
                            href="{{ route('employee.work.export.pdf', ['from_date' => request('from_date'), 'to_date' => request('to_date')]) }}"
                            class="bg-green-600 text-white px-4 py-2 rounded">
                            Export PDF
                        </a>

                    </form>

                </div>

                <!-- My Work Table -->
                <table id="workTable" class="w-full">
                    <thead>
                        <tr>
                            <th style="display:none;">Id</th>
                            <th>Date</th>
                            <th>Online</th>
                            <th>Offline</th>
                            <th>Total Hours</th>
                            <th>Project Name</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                            @php
                                $start = $session->start_time ? \Carbon\Carbon::parse($session->start_time) : null;
                                $end = $session->end_time ? \Carbon\Carbon::parse($session->end_time) : null;

                                // Calculate total duration including date difference
                                if ($start && $end) {
                                    $diffInSeconds = $end->diffInSeconds($start);
                                    $hours = floor($diffInSeconds / 3600);
                                    $minutes = floor(($diffInSeconds % 3600) / 60);
                                    $seconds = $diffInSeconds % 60;
                                    $total = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                                } else {
                                    $total = '-';
                                }
                            @endphp

                            <tr data-id="{{ $session->id }}">
                                <td style="display:none;">{{ $session->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($session->work_date)->format('d-m-Y') }}</td>

                                {{-- Start Time in 12-hour format --}}
                                <td>{{ $start ? $start->format('h:i A') : '-' }}</td>

                                {{-- End Time in 12-hour format --}}
                                <td>{{ $end ? $end->format('h:i A') : '-' }}</td>

                                {{-- Total duration including date difference --}}
                                <td>{{ $total }}</td>

                                <td contenteditable="true" onBlur="updateField(this, '{{ $session->id }}', 'project_name')">
                                    {{ $session->project_name }}
                                </td>
                                <td contenteditable="true" onBlur="updateField(this, '{{ $session->id }}', 'comment')">
                                    {{ $session->comment }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <!-- Styles -->
    <style>
        #workTable tr.odd { background-color: #f9fafb; }
        #workTable tr.even { background-color: #ffffff; }
    </style>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#workTable').DataTable({
                columnDefs: [
                    { targets: 0, visible: false, searchable: false }
                ],
                order: [[0, "desc"]],
                pageLength: 10,
                responsive: true,
                rowCallback: function (row, data, displayIndex) {
                    $(row).removeClass('odd even');
                    $(row).addClass(displayIndex % 2 === 0 ? 'even' : 'odd');
                }
            });
        });

        function updateField(el, id, field) {
            $.ajax({
                url: "/employee/" + id + "/inline-update",
                type: "POST",
                data: {
                    id: id,
                    field: field,
                    value: el.innerText,
                    _token: "{{ csrf_token() }}"
                }
            });
        }
        
        $('#att_export_pdf_btn').click(function () {
            let from = $('#att_from').val();
            let to = $('#att_to').val();

            //let url = `/employee/work/export-pdf?from_date=${from}&to_date=${to}`;
            //window.open(url, "_blank");
        });
    </script>
</x-app-layout>