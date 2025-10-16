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
							</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <!-- Modal -->
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
            let table = $('#leavesTable').DataTable({
                columnDefs: [
                    { targets: 0, visible: false, searchable: false },
                    { targets: -1, orderable: false }
                ],
                "order": [[0, "desc"]],
                "pageLength": 10,
                "responsive": true,
                "rowCallback": function (row, data, displayIndex) {
                    $(row).removeClass('odd even');
                    $(row).addClass(displayIndex % 2 === 0 ? 'even' : 'odd');
                }
            });

            $('#openModal').click(() => $('#modal').removeClass('hidden'));
            $('#closeModal').click(() => $('#modal').addClass('hidden'));

            // Apply Leave via AJAX
            $('#leaveForm').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route("employee.leaves.store") }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (data) {
                        $('#modal').addClass('hidden');

                        // format dates from YYYY-MM-DD to DD-MM-YYYY
                        function formatDate(dateStr) {
                            if (!dateStr) return '';
                            let [year, month, day] = dateStr.split("-");
                            return `${day}-${month}-${year}`;
                        }

                        let fromDate = formatDate(data.from_date);
                        let toDate   = formatDate(data.to_date);

                        let newRow = table.row.add([
                            data.id,
                            data.employee.name,
                            data.leave_type,
                            fromDate,
                            toDate,
                            `Pending`,
                            `
                            <a href="/employee/leaves/${data.id}" title="View">
                                <i class="fas fa-eye text-blue-500 mr-2"></i>
                            </a>
                            `
                        ]).draw().node();

                        $(newRow).attr('data-id', data.id);
                        $('#leaveForm')[0].reset();
                    },
                    error: function (xhr) {
                        alert(xhr.responseJSON?.message || 'Failed to create leave.');
                    }
                });
            });
        });
    </script>
</x-app-layout>