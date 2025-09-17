<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Employee Details
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">

            <!-- Tabs -->
            <ul id="tabs" class="flex border-b mb-6">
                <li class="-mb-px mr-1">
                    <a href="#tab-info"
                       class="tab-link bg-white inline-block border-l border-t border-r rounded-t py-2 px-4 font-semibold text-blue-600">
                       Employee Information
                    </a>
                </li>
                <li class="mr-1">
                    <a href="#tab-work"
                       class="tab-link bg-white inline-block py-2 px-4 text-gray-500 hover:text-blue-600">
                       Attendance
                    </a>
                </li>
            </ul>

            <!-- Tab Contents -->
            <div id="tab-contents">
                <!-- Employee Information Tab -->
                <div id="tab-info" class="tab-content p-4">
                    
                    <h3 class="text-lg font-bold mb-4">{{ $employee->name }}'s Info</h3>

                    <table class="w-full border-collapse border border-gray-300 mb-4">
                        @foreach (['name', 'email', 'employee_id', 'phone', 'position', 'pan_number', 'address', 'joining_date', 'bank_details'] as $field)
                            <tr>
                                <th class="border p-2 text-left capitalize">{{ str_replace('_', ' ', $field) }}</th>
                                <td class="border p-2">
                                    @if ($field === 'joining_date')
                                        <input
                                            type="date"
                                            class="joining-date-input"
                                            value="{{ $employee->$field }}"
                                            onchange="updateField({{ $employee->id }}, '{{ $field }}', this.value)"
                                        />
                                    @elseif ($field === 'bank_details')
                                        <span class="editable-textarea"
                                            data-field="{{ $field }}"
                                            data-id="{{ $employee->id }}">
                                            {{ $employee->$field ?: 'Add here....' }}
                                        </span>
                                    @else
                                        <span class="editable"
                                            data-field="{{ $field }}"
                                            data-id="{{ $employee->id }}">
                                            {{ $employee->$field }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <th class="border p-2 text-left">Documents</th>
                            <td class="border p-2">
                                <div id="documents-loading" class="hidden mb-2">
                                    <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    Uploading...
                                </div>
                                
                                <div id="documents-list" class="mb-2 text-sm">
                                    Loading...
                                </div>

                                <input type="file" id="document-upload" name="documents[]" multiple style="display: none;" />
                                <button type="button" onclick="document.getElementById('document-upload').click()"
                                    class="bg-blue-500 text-white px-3 py-1 rounded">
                                    Upload Document
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <th class="border p-2 text-left">Relieving Letter</th>
                            <td class="border p-2">
                                <a target="_blank" href="{{ route('employees.relieving-letter', $employee->id) }}">
                                    <button class="bg-blue-500 text-white px-4 py-2 rounded">Print</button>
                                </a>
                            </td>
                        </tr>
                    </table>

                    <a href="{{ route('employees.index') }}" 
                       class="inline-block bg-gray-600 text-white px-4 py-2 rounded">
                       ← Back
                    </a>

                </div>

                <!-- Attendance Tab -->
                <div id="tab-work" class="tab-content hidden p-4">
                    
                    <h3 class="text-lg font-bold mb-4">Attendance</h3>

                    <table id="workTable" class="w-full" style="width:100%;">
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
                                <td>{{ $session->start_time }}</td>
                                <td>{{ $session->end_time }}</td>
                                <td>{{ $total }}</td>
                                <td contenteditable="true" 
                                    onBlur="updateWorkField(this, '{{ $session->id }}', 'project_name')">
                                    {{ $session->project_name }}
                                </td>
                                <td contenteditable="true" 
                                    onBlur="updateWorkField(this, '{{ $session->id }}', 'comment')">
                                    {{ $session->comment }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>

    <!-- Common Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Tab Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tabs = document.querySelectorAll("#tabs .tab-link");
            const tabContents = document.querySelectorAll("#tab-contents .tab-content");

            tabs.forEach(tab => {
                tab.addEventListener("click", e => {
                    e.preventDefault();
                    tabs.forEach(t => t.classList.remove("border-l","border-t","border-r","rounded-t","text-blue-600","font-semibold"));
                    tabs.forEach(t => t.classList.add("text-gray-500"));
                    tabContents.forEach(c => c.classList.add("hidden"));

                    tab.classList.add("border-l","border-t","border-r","rounded-t","text-blue-600","font-semibold");
                    tab.classList.remove("text-gray-500");
                    document.querySelector(tab.getAttribute("href")).classList.remove("hidden");
                });
            });
        });
    </script>

    <!-- Employee Info Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.editable').forEach(makeInlineEditable);
            document.querySelectorAll('.editable-textarea').forEach(makeInlineTextareaEditable);
            loadEmployeeDocuments({{ $employee->id }});
        });

        function makeInlineEditable(element) { /* same as your code */ }
        function makeInlineTextareaEditable(element) { /* same as your code */ }
        function saveInlineEdit(inputElement, originalElement, id, field, oldValue) { /* same as your code */ }
        function updateField(id, field, value) { /* same as your code */ }

        // Document upload + list + delete (your code here)
        function loadEmployeeDocuments(employeeId) { /* same as your code */ }
        function deleteDocument(employeeId, documentId) { /* same as your code */ }
    </script>

    <!-- Work Table Scripts -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#workTable').DataTable({
                columnDefs: [{ targets: 0, visible: false, searchable: false }],
                order: [[0, "desc"]],
                pageLength: 10,
                responsive: true,
                rowCallback: function (row, data, displayIndex) {
                    $(row).removeClass('odd even');
                    $(row).addClass(displayIndex % 2 === 0 ? 'even' : 'odd');
                }
            });
        });

        function updateWorkField(el, id, field) {
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

    <!-- Styles -->
    <style>
        .joining-date-input { border: none; background: transparent; font: inherit; cursor: pointer; outline: none; }
        #workTable tr.odd { background-color: #f9fafb; }
        #workTable tr.even { background-color: #ffffff; }
    </style>
</x-app-layout>