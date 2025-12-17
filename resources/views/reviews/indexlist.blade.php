<x-app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

				<h3 class="text-lg font-bold mb-4">Feedback Management</h3>

                <!-- Add Review Button -->
                <div class="flex justify-end mb-4">
                    <button id="openModal" class="bg-blue-500 text-white px-4 py-2 rounded">Add Feedback</button>
                </div>
				
				<!-- Filters -->
                <!--<div class="flex gap-4 mb-4">
                    <div>
                        <label for="from_date" class="block text-sm font-medium text-gray-700">From Date</label>
                        <input type="date" id="from_date" class="border p-2 rounded">
                    </div>
                    <div>
                        <label for="to_date" class="block text-sm font-medium text-gray-700">To Date</label>
                        <input type="date" id="to_date" class="border p-2 rounded">
                    </div>
                    <div class="flex items-end">
                        <button id="filterBtn" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
                        <button id="resetBtn" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded">Reset</button>
                    </div>
                </div>->

                <!-- Reviews Table -->
                <table id="reviewsTable" class="w-full">
                    <thead>
                        <tr>
                            <th style="display:none;">Id</th>
                            <th>Employee</th>
                            <th>Project</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Feedback Given By</th>
                            <th>Feedback</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $review)
                        <tr data-id="{{ $review->id }}">
                            <td style="display:none;">{{ $review->id }}</td>
                            <td>{{ $review->employee->name }}</td>
                            <td>{{ $review->project_name }}</td>
                            <td>
								{{ \Carbon\Carbon::parse($review->date_from)->format('d/m/Y') }}
							</td>
							<td>
								{{ \Carbon\Carbon::parse($review->date_to)->format('d/m/Y') }}
							</td>
                                                        <td>
                                                            {{ $review->review_given_by }}
                                                        </td>
                            <td>{{ $review->review }}</td>
                            <td>
                                <a href="/employee/reviews-list/{{ $review->id }}">
                                    <i class="fas fa-eye text-blue-500 mr-2"></i>
                                </a>
                                <form method="POST" action="{{ route('employee.review.destroy', $review->id) }}" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete this feedback?')">
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
		  <h2 class="text-lg font-semibold mb-4">Add Feedback</h2>
		  <form id="reviewForm">
			 @csrf 
			 <select name="employee_id" required class="w-full mb-2 border p-2">
				<option value="">Select Employee</option>
				@foreach($employees as $employee) 
				<option value="{{ $employee->id }}">{{ $employee->name }}</option>
				@endforeach 
			 </select>
			 <input type="text" name="project_name" placeholder="Project Name" class="w-full mb-2 border p-2" required>
			 <input type="date" name="date_from" class="w-full mb-2 border p-2" required>
			 <input type="date" name="date_to" class="w-full mb-2 border p-2" required>
                         <input type="hidden" name="review_given_by" placeholder="Feedback Given By" class="w-full mb-2 border p-2" value="@if(Auth::user()->id === 101){{ Auth::user()->id }}@else{{ auth('employee')->id() }}@endif" required>
			 <textarea name="review" placeholder="Write Feedback..." class="w-full mb-2 border p-2" required></textarea>
			 <div class="flex justify-end gap-2"> <button type="button" id="closeModal" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button> <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Save</button> </div>
		  </form>
	   </div>
	</div>

    <!-- Styles -->
    <style>
        #reviewsTable tr.odd { background-color: #f9fafb; }
        #reviewsTable tr.even { background-color: #ffffff; }
    </style>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize DataTable
            let table = $('#reviewsTable').DataTable({
                columnDefs: [
                    { targets: 0, visible: false, searchable: false }, // Hide ID column
                    { targets: -1, orderable: false } // Disable sorting for Actions column
                ],
                order: [[0, "desc"]],
                pageLength: 10,
                responsive: true,
                rowCallback: function (row, data, displayIndex) {
                    $(row).removeClass('odd even');
                    if (displayIndex % 2 === 0) {
                        $(row).addClass('even');
                    } else {
                        $(row).addClass('odd');
                    }
                }
            });

            // Custom Date Filter
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                let from = $('#from_date').val();
                let to = $('#to_date').val();
                let dateFrom = data[3] || ''; // From column index
                let dateTo = data[4] || '';   // To column index

                if (!from && !to) return true;

                let fromDate = from ? new Date(from) : null;
                let toDate = to ? new Date(to) : null;
                let rowDateFrom = dateFrom ? new Date(dateFrom) : null;
                let rowDateTo = dateTo ? new Date(dateTo) : null;

                if (fromDate && rowDateFrom < fromDate) return false;
                if (toDate && rowDateTo > toDate) return false;

                return true;
            });

            // Filter button
            $('#filterBtn').on('click', function () {
                table.draw();
            });

            // Reset button
            $('#resetBtn').on('click', function () {
                $('#from_date, #to_date').val('');
                table.draw();
            });

            // Existing Add Review Modal code...
            $('#openModal').click(() => $('#modal').removeClass('hidden'));
            $('#closeModal').click(() => $('#modal').addClass('hidden'));

            // Add Review AJAX
            $('#reviewForm').submit(function(e){
                e.preventDefault();
                $.ajax({
                    url: '{{ route("employee.review.store") }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(data){
                        $('#modal').addClass('hidden');
                        /*let newRow = table.row.add([
                            data.id,
                            data.employee.name,
                            data.project_name,
                            data.date_from,
                            data.date_to,
                            data.review_given_by,
                            data.review,
                            `
                            <a href="/employee/reviews-list/${data.id}">
                                <i class="fas fa-eye text-blue-500 mr-2"></i>
                            </a>
                            <form method="POST" action="/employee/reviews-list/${data.id}" style="display:inline">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button onclick="return confirm('Delete this feedback?')">
                                    <i class="fas fa-trash-alt text-red-500"></i>
                                </button>
                            </form>
                            `
                        ]).draw().node();

                        $(newRow).attr('data-id', data.id);
                        $('#reviewForm')[0].reset();*/
                        
                        location.reload();
                    }
                });
            });

            // Inline Edit (unchanged)
            $(document).on('blur', '.editable', function() {
                let id = $(this).closest('tr').data('id');
                let field = $(this).data('field');
                let value = $(this).text();

                $.ajax({
                    url: `/employee/reviews-list/${id}`,
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        [field]: value
                    },
                    success: function() {
                        console.log('Updated successfully');
                    },
                    error: function() {
                        alert('Update failed');
                    }
                });
            });
			
			// Inline Edit for Date Pickers
			$(document).on('change', '.editable-date', function() {
				let id = $(this).closest('tr').data('id');
				let field = $(this).data('field');
				let value = $(this).val();

				$.ajax({
					url: `/employee/reviews-list/${id}`,
					method: 'PUT',
					data: {
						_token: '{{ csrf_token() }}',
						[field]: value
					},
					success: function() {
						console.log('Date updated successfully');
					},
					error: function() {
						alert('Date update failed');
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