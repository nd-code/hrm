<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Leaves') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <!-- Add Leave Button -->
                <div class="flex justify-end mb-4">
                    <button id="openModal" class="bg-blue-500 text-white px-4 py-2 rounded">Add Leave</button>
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
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaves as $leave)
                        <tr data-id="{{ $leave->id }}">
                            <td style="display:none;">{{ $leave->id }}</td>
                            <td>{{ $leave->employee->name }}</td>
							<td contenteditable="true" class="editable" data-field="leave_type">{{ $leave->leave_type }}</td>
                            <td contenteditable="true" class="editable" data-field="from_date">{{ $leave->from_date }}</td>
                            <td contenteditable="true" class="editable" data-field="to_date">{{ $leave->to_date }}</td>
                            <td contenteditable="true" class="editable" data-field="reason">{{ $leave->reason }}</td>
                            <td>{{ ucfirst($leave->status) }}@if($leave->status == 'Pending')  (<button class="approve-btn text-green-500 ml-2 mr-2" title="Approve" data-id="{{ $leave->id }}"><i class="fas fa-check-circle"></i></button><button class="reject-btn text-red-500 mr-2" title="Reject" data-id="{{ $leave->id }}"><i class="fas fa-times-circle"></i></button>)@endif</td>
                            <td>
								<a href="{{ route('leaves.show', $leave->id) }}" title="View">
									<i class="fas fa-eye text-blue-500 mr-2"></i>
								</a>
								<form method="POST" action="{{ route('leaves.destroy', $leave->id) }}" style="display:inline">
									@csrf
									@method('DELETE')
									<button onclick="return confirm('Delete this leave?')" title="Delete">
										<i class="fas fa-trash-alt text-red-500"></i>
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

    <!-- Modal -->
    <div id="modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded shadow-lg w-1/3">
            <h2 class="text-lg font-semibold mb-4">Add Leave</h2>
            <form id="leaveForm">
                @csrf
                <select name="employee_id" required class="w-full mb-2 border p-2">
                    <option value="">Select Employee</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                </select>
				<select name="leave_type" id="leave_type" class="w-full mb-2 border p-2" required>
                    <option value="">Select Leave Type</option>
                    <option value="Sick Leave">Sick Leave</option>
                    <option value="Casual Leave">Casual Leave</option>
                    <option value="Paid Leave">Paid Leave</option>
                    <option value="Unpaid Leave">Unpaid Leave</option>
                </select>
                <input type="date" name="from_date" class="w-full mb-2 border p-2" required>
                <input type="date" name="to_date" class="w-full mb-2 border p-2" required>
                <textarea name="reason" placeholder="Reason for leave..." class="w-full mb-2 border p-2" required></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" id="closeModal" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Save</button>
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

            // Add Leave via AJAX
            $('#leaveForm').submit(function (e) {
				e.preventDefault();
				$.ajax({
					url: '{{ route("leaves.store") }}',
					method: 'POST',
					data: $(this).serialize(),
					success: function (data) {
						$('#modal').addClass('hidden');

						let newRow = table.row.add([
							data.id,
							data.employee.name,
							data.leave_type,
							data.from_date,
							data.to_date,
							data.reason,
							`Pending (<button class="approve-btn text-green-500 ml-2 mr-2" title="Approve" data-id="${data.id}">
								<i class="fas fa-check-circle"></i>
							 </button>
							 <button class="reject-btn text-red-500 mr-2" title="Reject" data-id="${data.id}">
								<i class="fas fa-times-circle"></i>
							 </button>)`,
							`
							<a href="/leaves/${data.id}" title="View">
								<i class="fas fa-eye text-blue-500 mr-2"></i>
							</a>
							<form method="POST" action="/leaves/${data.id}" style="display:inline">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="hidden" name="_method" value="DELETE">
								<button onclick="return confirm('Delete this leave?')" title="Delete">
									<i class="fas fa-trash-alt text-red-500"></i>
								</button>
							</form>
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

            // Inline Edit
            $(document).on('blur', '.editable', function() {
                let id = $(this).closest('tr').data('id');
                let field = $(this).data('field');
                let value = $(this).text();

                $.ajax({
                    url: `/leaves/${id}`,
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        [field]: value
                    }
                });
            });

            // Approve/Reject Leave
			$(document).on('click', '.approve-btn, .reject-btn', function() {
				let id = $(this).data('id');
				let status = $(this).hasClass('approve-btn') ? 'approved' : 'rejected';

				$.ajax({
					url: `/leaves/${id}/status`,
					method: 'PUT',
					data: {
						_token: '{{ csrf_token() }}',
						status: status
					},
					success: function(resp) {
						// Update the status cell in the same row
						const row = $(`#leavesTable tr[data-id="${id}"]`);
						// Status is in the 7th column (index 6) based on your markup
						row.find('td').eq(5).text(resp.status);

						// Remove the approve/reject buttons after update
						row.find('.approve-btn, .reject-btn').remove();
					},
					error: function(xhr){
						alert(xhr.responseJSON?.message || 'Failed to update status.');
					}
				});
			});
        });
    </script>
</x-app-layout>