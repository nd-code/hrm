@php
	use Carbon\Carbon;
@endphp
<x-app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

				<h3 class="text-lg font-bold mb-4">Leave Management</h3>

                <!-- Add Leave Button -->
                <div class="flex justify-end mb-4">
                    <button id="openModal" class="bg-blue-500 text-white px-4 py-2 rounded">Add Leave</button>
                </div>
				
				<!-- Filters -->
				<div class="mb-4 flex space-x-4">
					<select id="employee_filter" class="border p-2 rounded">
						<option value="">All Employees</option>
						@foreach($employees as $employee)
							<option value="{{ $employee->id }}">{{ $employee->name }}</option>
						@endforeach
					</select>

					<input type="date" id="from_date" class="border p-2 rounded">
					<input type="date" id="to_date" class="border p-2 rounded">

					<button id="filterBtn" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
					
					<button type="button" id="resetBtn" class="bg-gray-400 text-white px-4 py-2 rounded">
						Reset
					</button>
				</div>

                <!-- Leaves Table -->
                <table id="leavesTable" class="w-full">
                    <thead>
                        <tr>
                            <th style="display:none;">Id</th>
                            <th>Employee</th>
							<th>Apply To</th>
							<th>Leave Type</th>
                            <th>From</th>
                            <th>To</th>
							<th>Number of Day(s)</th>
                            <th>Reason</th>
							<th>Managed By</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaves as $leave)
                        <tr data-id="{{ $leave->id }}">
                            <td style="display:none;">{{ $leave->id }}</td>
                            <td>{{ $leave->employee->name }}</td>
							<td>
								@php
									$ids = explode(',', $leave->apply_to ?? '');
									$names = \App\Models\Employee::whereIn('id', $ids)->pluck('name')->toArray();
								@endphp
								{{ implode(', ', $names) }}
							</td>
							<td contenteditable="true" class="editable" data-field="leave_type">{{ $leave->leave_type }}</td>
                            <td>
								<input type="date" class="editable-date" data-field="from_date"
									   value="{{ $leave->from_date }}">
							</td>
							<td>
								<input type="date" class="editable-date" data-field="to_date"
									   value="{{ $leave->to_date }}">
							</td>
							<td id="leave-days">
								@php
									if(isset($leave->leave_type) && $leave->leave_type == 'Half Day Leave')
									{
										$days = '0.5';
									}
									else
									{
										$fromDate = Carbon::parse($leave->from_date);
										$toDate = Carbon::parse($leave->to_date);
										$days = $fromDate->diffInDays($toDate) + 1; // +1 if both dates are inclusive
									}
								@endphp
								{{ $days }} Day(s)
							</td>
                            <td contenteditable="true" class="editable" data-field="reason">{{ $leave->reason }}</td>
							<td>{{ $leave->manager?->name ?? '-' }}</td>
                            <td>{{ ucfirst($leave->status) }}@if($leave->status == 'Pending')  (<button class="approve-btn text-green-500 ml-2 mr-2" title="Approve" data-id="{{ $leave->id }}"><i class="fas fa-check-circle"></i></button><button class="reject-btn text-red-500 mr-2" title="Reject" data-id="{{ $leave->id }}"><i class="fas fa-times-circle"></i></button>)@endif</td>
                            <td>
								<a href="{{ route('leaves.show', $leave->id) }}" title="View">
									<i class="fas fa-eye text-blue-500 mr-2"></i>
								</a>
								<form method="POST" action="{{ route('leaves.destroy', $leave->id) }}" style="display:inline">
									@csrf
									@method('DELETE')
									<button type="submit" class="text-red-500 hover:text-red-700" title="Delete" onclick="return confirm('Delete this leave?')">
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

    <!-- Modal -->
    <div id="modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded shadow-lg w-1/3">
            <h2 class="text-lg font-semibold mb-4">Add Leave</h2>
            <form id="leaveForm">
                @csrf
                <select name="employee_ids[]" multiple required class="w-full mb-2 border p-2">
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
	
	<!-- Manage By Modal -->
	<div id="manageByModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
		<div class="bg-white p-6 rounded shadow-lg w-96">
			<h2 class="text-lg font-bold mb-4">Select Manager</h2>
			<form id="manageByForm">
				<select name="manage_by" id="manage_by" class="w-full border p-2 mb-4">
					@foreach($employees as $employee)
						<option value="{{ $employee->id }}">{{ $employee->name }}</option>
					@endforeach
				</select>
				<input type="hidden" name="leave_id" id="leave_id">
				<input type="hidden" name="status" id="leave_status">
				<button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Save</button>
				<!--<button type="button" class="ml-2 bg-gray-400 text-white px-4 py-2 rounded" onclick="closeManageByModal()">Cancel</button>-->
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
			// Disable DataTables default alert popup
			$.fn.dataTable.ext.errMode = 'none';
			
            let table = $('#leavesTable').DataTable({
				processing: true,
				serverSide: false, // since you are returning all data after filter
				ajax: {
					url: '{{ route("leaves.index") }}',
					data: function (d) {
						d.employee_id = $('#employee_filter').val();
						d.from_date   = $('#from_date').val();
						d.to_date     = $('#to_date').val();
					},
					dataSrc: 'data' // your JSON response has "data" key
				},
				columns: [
					{ data: 'id', visible: false },

					// Employee
					{ data: 'employee.name', defaultContent: '-' },

					// Apply To
					{ 
						data: 'apply_to_names',
						className: 'editable',
						render: function (data, type, row) {
							return data || '-';
						}
					},

					// Leave Type
					{ 
						data: 'leave_type',
						render: function (data, type, row) {
							return `<span contenteditable="true" 
										  class="editable" 
										  data-id="${row.id}" 
										  data-field="leave_type">${data || ''}</span>`;
						}
					},

					// From Date
					{ 
						data: 'from_date',
						render: function (data, type, row) {
							return `<input type="date" class="editable-date" data-id="${row.id}" data-field="from_date" value="${data || ''}">`;
						}
					},

					// To Date
					{ 
						data: 'to_date',
						render: function (data, type, row) {
							return `<input type="date" class="editable-date" data-id="${row.id}" data-field="to_date" value="${data || ''}">`;
						}
					},

					// Number of days
					{ 
						data: null,
						render: function (row) {
							let days = 0;
							if (row.leave_type === 'Half Day Leave') {
								days = 0.5;
							} else {
								let from = new Date(row.from_date);
								let to   = new Date(row.to_date);
								days = Math.floor((to - from) / (1000*60*60*24)) + 1;
							}
							return `<span id="leave-days-${row.id}">${days} Day(s)</span>`;
						}
					},

					// Reason
					{ 
						data: 'reason',
						render: function (data, type, row) {
							return `<span contenteditable="true" 
										  class="editable" 
										  data-id="${row.id}" 
										  data-field="reason">${data || ''}</span>`;
						}
					},

					// Managed By
					{ data: 'manager.name', defaultContent: '-' },

					// Status
					{ 
						data: 'status',
						render: function (data, type, row) {
							let status = data ? data.charAt(0).toUpperCase() + data.slice(1) : '';
							if (data === 'Pending') {
								return `${status} 
									(<button class="approve-btn text-green-500 ml-2 mr-2" title="Approve" data-id="${row.id}">
										<i class="fas fa-check-circle"></i>
									</button>
									<button class="reject-btn text-red-500 mr-2" title="Reject" data-id="${row.id}">
										<i class="fas fa-times-circle"></i>
									</button>)`;
							}
							return status;
						}
					},

					// Actions
					{ 
						data: null,
						orderable: false,
						render: function (row) {
							return `<a href="/leaves/${row.id}" title="View">
										<i class="fas fa-eye text-blue-500 mr-2"></i>
									</a>
									<form method="POST" action="/leaves/${row.id}" style="display:inline">
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
										<input type="hidden" name="_method" value="DELETE">
										<button onclick="return confirm('Delete this leave?')" title="Delete">
											<i class="fas fa-trash-alt text-red-500"></i>
										</button>
									</form>`;
						}
					}
				],
				order: [[0, "desc"]],
				pageLength: 10,
				responsive: true,
				rowCallback: function (row, data, displayIndex) {
					$(row).removeClass('odd even');
					$(row).addClass(displayIndex % 2 === 0 ? 'even' : 'odd');
				}
			});

			// Filter button reload
			$('#filterBtn').on('click', function () {
				table.ajax.reload();
			});
			
			// Reset button
			$('#resetBtn').on('click', function () {
				$('#employee_filter').val('');
				$('#from_date').val('');
				$('#to_date').val('');
				table.ajax.url('{{ route("leaves.index") }}').load();
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
						
						location.reload();
					},
					error: function (xhr) {
						alert(xhr.responseJSON?.message || 'Failed to create leave.');
					}
				});
			});

            // Inline text editing (on blur)
			$(document).on('blur', '.editable', function () {
				let id    = $(this).data('id');
				let field = $(this).data('field');
				let value = $(this).text().trim();

				$.ajax({
					url: `/leaves/${id}`,   // update endpoint
					type: 'PUT',
					data: {
						_token: '{{ csrf_token() }}',
						[field]: value
					},
					success: function (res) {
						console.log("Updated", res);
					},
					error: function (xhr) {
						alert("Update failed: " + xhr.responseText);
					}
				});
			});

            // Approve/Reject Leave
			let selectedLeaveId = null;
			let selectedStatus = null;

			$(document).on('click', '.approve-btn, .reject-btn', function () {
				selectedLeaveId = $(this).data('id');
				selectedStatus = $(this).hasClass('approve-btn') ? 'approved' : 'rejected';
				$('#leave_id').val(selectedLeaveId);
				$('#leave_status').val(selectedStatus);
				$('#manageByModal').removeClass('hidden');
			});

			function closeManageByModal() {
				$('#manageByModal').addClass('hidden');
			}

			$('#manageByForm').on('submit', function (e) {
				e.preventDefault();

				const leaveId = $('#leave_id').val();
				const manageBy = $('#manage_by').val();
				const status = $('#leave_status').val();

				$.ajax({
					url: `/leaves/${leaveId}/status`,
					method: 'PUT',
					data: {
						_token: '{{ csrf_token() }}',
						status: status,
						manage_by: manageBy
					},
					success: function (resp) {
						const row = $(`#leavesTable tr[data-id="${leaveId}"]`);
						row.find('td').eq(7).text(resp.manager);
						row.find('td').eq(8).text(resp.status);
						row.find('.approve-btn, .reject-btn').remove();

						closeManageByModal();
					},
					error: function (xhr) {
						alert(xhr.responseJSON?.message || 'Failed to update status.');
					}
				});
			});
			
			// Inline date editing (on change)
			$(document).on('change', '.editable-date', function () {
				let id    = $(this).data('id');
				let field = $(this).data('field');
				let value = $(this).val();

				$.ajax({
					url: `/leaves/${id}`,
					type: 'PUT',
					data: {
						_token: '{{ csrf_token() }}',
						[field]: value
					},
					success: function (res) {
						console.log("Date updated", res);
						
						if (res.success && res.days !== undefined) {
							document.getElementById('leave-days-'+id).textContent = res.days + ' Day(s)';
						}
					},
					error: function (xhr) {
						alert("Update failed: " + xhr.responseText);
					}
				});
			});
        });
    </script>

	<style>
		.editable-date {
			border: none;
			background: transparent;
			font: inherit;
			cursor: pointer;
		}

		.editable-date {
			outline: none;
		}
	</style>
</x-app-layout>