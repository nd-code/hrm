<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Work') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <!-- My Work Table -->
                <table id="workTable" class="w-full">
                    <thead>
                        <tr>
                            <th style="display:none;">Id</th>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
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
                            $total = ($start && $end) ? $start->diff($end)->format('%H:%I:%S') : '-';
                        @endphp
                        <tr data-id="{{ $session->id }}">
                            <td style="display:none;">{{ $session->id }}</td>
                            <td>{{ $session->work_date }}</td>
                            <td>
								{{ $session->start_time ? \Carbon\Carbon::parse($session->start_time)->format('H:i:s') : '-' }}
							</td>
							<td>
								{{ $session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('H:i:s') : '-' }}
							</td>
                            <td>{{ $total }}</td>
                            <td contenteditable="true" 
                                onBlur="updateField(this, '{{ $session->id }}', 'project_name')">
                                {{ $session->project_name }}
                            </td>
                            <td contenteditable="true" 
                                onBlur="updateField(this, '{{ $session->id }}', 'comment')">
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
    </script>
</x-app-layout>