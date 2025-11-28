<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Leaves') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                
                <h3 class="text-lg font-bold mb-4">Leaves</h3>

                <!-- Apply Leave Button -->
                <div class="flex justify-end mb-4">
                    <button id="openModal" class="bg-blue-500 text-white px-4 py-2 rounded">Apply Leave</button>
                </div>

                <!-- Leaves Table -->
                <table id="leavesTable" class="w-full">
                    <thead>
                        <tr>
                            <th style="display:none;">Id</th>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaves as $leave)
                        <tr data-id="{{ $leave->id }}">
                            <td style="display:none;">{{ $leave->id }}</td>
                            <td>{{ $leave->employee->name }}</td>
                            <td>{{ $leave->leave_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->from_date)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->to_date)->format('d-m-Y') }}</td>
                            <td>{{ ucfirst($leave->status) }}</td>
                            <td>
                                <a href="{{ route('employee.leaves.show', $leave->id) }}" title="View">
                                    <i class="fas fa-eye text-blue-500 mr-2"></i>
                                </a>
                                @if(in_array(Auth::user()->position, ['1', '2', '3', '10']))
                                    
                                @elseif($leave->status === 'Pending')
                                    <button class="reply-btn text-blue-500 mr-2" title="Reply" data-id="{{ $leave->id }}">
                                        <i class="fas fa-reply"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <!-- Apply Leave Modal -->
    <div id="modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded shadow-lg w-1/3">
            <h2 class="text-lg font-semibold mb-4">Apply Leave</h2>
            <form id="leaveForm">
                @csrf
                <select name="leave_type" id="leave_type" class="w-full mb-2 border p-2" required>
                    <option value="">Select Leave Type</option>
                    <option value="Half Day Leave">Half Day Leave</option>
                    <option value="Sick Leave">Sick Leave</option>
                    <option value="Casual Leave">Casual Leave</option>
                </select>
                <label>From Date:</label>
                <input type="date" name="from_date" class="w-full mb-2 border p-2" required>
                <label>To Date:</label>
                <input type="date" name="to_date" class="w-full mb-2 border p-2" required>
                <textarea name="reason" placeholder="Reason for leave..." class="w-full mb-2 border p-2" required></textarea>
                <label>Email To:</label>
                <select name="employee_ids[]" multiple required class="w-full mb-2 border p-2">
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                </select>
                <div class="flex justify-end gap-2">
                    <button type="button" id="closeModal" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reply Thread Modal -->
    <div id="replyModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded shadow-lg w-96">
            <h2 class="text-lg font-semibold mb-3">Leave Discussion</h2>

            <div id="repliesContainer" class="max-h-60 overflow-y-auto border p-2 mb-3 text-sm">
                <p class="text-gray-500 italic">Loading...</p>
            </div>

            <form id="replyForm">
                @csrf
                <textarea name="message" id="replyMessage" placeholder="Type your reply..." class="w-full border p-2 mb-3" required></textarea>
                <input type="hidden" name="leave_id" id="reply_leave_id">
                <div class="flex justify-end gap-2">
                    <button type="button" id="closeReplyModal" class="bg-gray-500 text-white px-4 py-2 rounded">Close</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Send</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Styles -->
    <style>
        #leavesTable tr.odd { background-color: #f9fafb; }
        #leavesTable tr.even { background-color: #ffffff; }
    </style>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <script>
    $(document).ready(function () {
        // Initialize DataTable
        let table = $('#leavesTable').DataTable({
            columnDefs: [
                { targets: 0, visible: false, searchable: false },
                { targets: -1, orderable: false }
            ],
            order: [[0, "desc"]],
            pageLength: 10,
            responsive: true,
            rowCallback: function (row, data, displayIndex) {
                $(row).removeClass('odd even');
                $(row).addClass(displayIndex % 2 === 0 ? 'even' : 'odd');
            }
        });

        // Apply Leave Modal
        $('#openModal').click(() => $('#modal').removeClass('hidden'));
        $('#closeModal').click(() => $('#modal').addClass('hidden'));

        // Submit new leave
        $('#leaveForm').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: '{{ route("employee.leaves.store") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function (data) {
                    $('#modal').addClass('hidden');
                    $('#leaveForm')[0].reset();
                    alert('Leave applied successfully!');
                    location.reload();
                },
                error: function (xhr) {
                    alert(xhr.responseJSON?.message || 'Failed to create leave.');
                }
            });
        });

        // === Reply Thread Logic ===
        $(document).on('click', '.reply-btn', function () {
            const leaveId = $(this).data('id');
            $('#reply_leave_id').val(leaveId);
            $('#replyModal').removeClass('hidden');
            $('#repliesContainer').html('<p class="text-gray-500 italic">Loading...</p>');

            // Load existing replies
            $.get(`/leaves/${leaveId}/replies`, function (replies) {
                let html = '';
                if (replies.length === 0) {
                    html = '<p class="text-gray-500 italic">No replies yet.</p>';
                } else {
                    replies.forEach(r => {
                        html += `
                            <div class="mb-2 border-b pb-1">
                                <strong>${r.employee?.name || 'Admin'}</strong>
                                <span class="text-xs text-gray-400">${new Date(r.created_at).toLocaleString()}</span><br>
                                ${r.message}
                            </div>
                        `;
                    });
                }
                $('#repliesContainer').html(html);
            });
        });

        // Close reply modal
        $('#closeReplyModal').on('click', function () {
            $('#replyModal').addClass('hidden');
            $('#replyMessage').val('');
        });

        // Submit reply
        $('#replyForm').on('submit', function (e) {
            e.preventDefault();
            const leaveId = $('#reply_leave_id').val();
            const message = $('#replyMessage').val();

            $.post(`/employee/leaves/${leaveId}/reply`, {
                _token: '{{ csrf_token() }}',
                message: message
            }).done(function (resp) {
                const r = resp.reply;
                $('#replyMessage').val('');
                $('#repliesContainer').append(`
                    <div class="mb-2 border-b pb-1">
                        <strong>${r.employee?.name || 'Admin'}</strong>
                        <span class="text-xs text-gray-400">${new Date(r.created_at).toLocaleString()}</span><br>
                        ${r.message}
                    </div>
                `);
            }).fail(function (xhr) {
                alert(xhr.responseJSON?.message || 'Failed to send reply.');
            });
        });
    });
    </script>
</x-app-layout>