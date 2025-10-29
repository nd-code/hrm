<x-app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h3 class="text-lg font-bold mb-4">Reminders</h3>

                    <!-- Set Reminder Button -->
                    <div class="flex justify-end mb-4">
                        <button id="openReminderModal" class="bg-blue-500 text-white px-4 py-2 rounded">
                            Set Reminder
                        </button>
                    </div>

                    <!-- Set Reminder Modal -->
                    <div id="addReminderModal" class="fixed inset-0 hidden bg-gray-800 bg-opacity-50 flex items-center justify-center" style="z-index: 1;">
                        <div class="bg-white rounded-lg p-6 w-1/3">
                            <h3 class="text-lg font-bold mb-4">Set Reminder</h3>
                            <form id="addReminderForm">
                                @csrf
                                <input type="date" name="date" class="border p-2 w-full mb-2" required>
                                <input type="text" name="subject" placeholder="Subject" class="border p-2 w-full mb-2" required>
                                <textarea name="description" placeholder="Description" class="border p-2 w-full mb-2"></textarea>
                                
                                <div class="flex justify-end">
                                    <button type="button" id="closeReminderModal" class="bg-gray-400 text-white px-4 py-2 rounded mr-2">Cancel</button>
                                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded savebtn">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Reminder Table -->
                    <table id="remindersTable" class="display w-full mt-4">
                        <thead>
                            <tr>
                                <th style="display:none;">ID</th>
                                <th>Date</th>
                                <th>Subject</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reminders as $reminder)
                            <tr data-id="{{ $reminder->id }}">
                                <td style="display:none;">{{ $reminder->id }}</td>
                                <td>
                                    <input type="date" value="{{ $reminder->date }}"
                                        onchange="updateReminderField({{ $reminder->id }}, 'date', this.value)" />
                                </td>
                                <td contenteditable="true" onBlur="updateReminderField({{ $reminder->id }}, 'subject', this.innerText)">
                                    {{ $reminder->subject }}
                                </td>
                                <td contenteditable="true" onBlur="updateReminderField({{ $reminder->id }}, 'description', this.innerText)">
                                    {{ $reminder->description }}
                                </td>
                                <td contenteditable="false">
                                    {{ $reminder->status }}
                                </td>
                                <td class="space-x-2">
                                    <a href="{{ route('reminders.show', $reminder->id) }}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-eye"></i></a>
                                    <form method="POST" action="{{ route('reminders.destroy', $reminder->id) }}" style="display:inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Delete this reminder?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#remindersTable').DataTable({
            columnDefs: [
                { targets: 0, visible: false, searchable: false },
                { targets: -1, orderable: false }
            ],
            "order": [[0, "desc"]],
            "pageLength": 10,
            "responsive": true
        });
    });

    // Modal controls
    $('#openReminderModal').click(function() { $('#addReminderModal').removeClass('hidden'); });
    $('#closeReminderModal').click(function() { $('#addReminderModal').addClass('hidden'); });

    // Set Reminder AJAX
    $('#addReminderForm').submit(function(e) {
        e.preventDefault();
        $('.savebtn').prop('disabled', true);

        $.ajax({
            url: "{{ route('reminders.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#addReminderModal').addClass('hidden');
                var table = $('#remindersTable').DataTable();
                table.row.add([
                    `${response.id}`,
                    `<input type="date" value="${response.date}" onchange="updateReminderField(${response.id}, 'date', this.value)" />`,
                    `<td contenteditable="true" onBlur="updateReminderField(${response.id}, 'subject', this.innerText)">${response.subject}</td>`,
                    `<td contenteditable="true" onBlur="updateReminderField(${response.id}, 'description', this.innerText)">${response.description ?? ''}</td>`,
                    `<td contenteditable="false">Pending</td>`,
                    `<td class="space-x-2">
                        <a href="/reminders/${response.id}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-eye"></i></a>
                        <form method="POST" action="/reminders/${response.id}" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Delete this reminder?')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>`
                ]).order([0, 'desc']).draw(false);

                $('#addReminderForm')[0].reset();
                $('.savebtn').prop('disabled', false);
            }
        });
    });

    // Inline update
    function updateReminderField(id, field, value) {
        $.post(`/employee/reminders/${id}/inline-update`, {
            _token: '{{ csrf_token() }}',
            [field]: value
        });
    }
    </script>
</x-app-layout>