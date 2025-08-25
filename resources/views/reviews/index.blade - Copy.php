<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reviews') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <!-- Add Review Button -->
                <div class="flex justify-end mb-4">
                    <button id="openModal" class="bg-blue-500 text-white px-4 py-2 rounded">Add Review</button>
                </div>

                <!-- Reviews Table -->
                <table border="1" cellpadding="10" class="w-full">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Project</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Review</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="reviewTable">
                        @foreach($reviews as $review)
                        <tr data-id="{{ $review->id }}">
							<td>{{ $review->employee->name }}</td>
							<td contenteditable="true" class="editable" data-field="project_name">{{ $review->project_name }}</td>
							<td contenteditable="true" class="editable" data-field="date_from">{{ $review->date_from }}</td>
							<td contenteditable="true" class="editable" data-field="date_to">{{ $review->date_to }}</td>
							<td contenteditable="true" class="editable" data-field="review">{{ $review->review }}</td>
							<td>
								<a href="{{ route('reviews.show', $review->id) }}">
									<i class="fas fa-eye text-blue-500 mr-2"></i>
								</a>
								<form method="POST" action="{{ route('reviews.destroy', $review->id) }}" style="display:inline">
									@csrf
									@method('DELETE')
									<button onclick="return confirm('Delete?')">
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
            <h2 class="text-lg font-semibold mb-4">Add Review</h2>
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
                <textarea name="review" placeholder="Write review..." class="w-full mb-2 border p-2" required></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" id="closeModal" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        $('#openModal').click(() => $('#modal').removeClass('hidden'));
        $('#closeModal').click(() => $('#modal').addClass('hidden'));

        $('#reviewForm').submit(function(e){
            e.preventDefault();
            $.ajax({
                url: '{{ route("reviews.store") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(data){
					$('#modal').addClass('hidden');
					$('#reviewTable').prepend(`
						<tr>
							<td>${data.employee.name}</td>
							<td>${data.project_name}</td>
							<td>${data.date_from}</td>
							<td>${data.date_to}</td>
							<td>${data.review}</td>
							<td>
								<a href="/reviews/${data.id}">
									<i class="fas fa-eye text-blue-500 mr-2"></i>
								</a>
								<form method="POST" action="/reviews/${data.id}" style="display:inline">
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
									<input type="hidden" name="_method" value="DELETE">
									<button onclick="return confirm('Delete?')">
										<i class="fas fa-trash-alt text-red-500"></i>
									</button>
								</form>
							</td>
						</tr>
					`);
					$('#reviewForm')[0].reset();
				}
            });
        });
		
		$(document).on('blur', '.editable', function() {
			let id = $(this).closest('tr').data('id');
			let field = $(this).data('field');
			let value = $(this).text();

			$.ajax({
				url: `/reviews/${id}`,
				method: 'PUT',
				data: {
					_token: '{{ csrf_token() }}',
					[field]: value
				},
				success: function(response) {
					console.log('Updated successfully');
				},
				error: function() {
					alert('Update failed');
				}
			});
		});
    </script>
</x-app-layout>