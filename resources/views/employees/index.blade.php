<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employees') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Add Employee Button -->
                    <div class="flex justify-end mb-4">
                        <button id="openModal" class="bg-blue-500 text-white px-4 py-2 rounded">
                            Add Employee
                        </button>
                    </div>

                    <!-- Add Employee Modal -->
                    <div id="addEmployeeModal" class="fixed inset-0 hidden bg-gray-800 bg-opacity-50 flex items-center justify-center" style="z-index: 1;">
                        <div class="bg-white rounded-lg p-6 w-1/3">
                            <h3 class="text-lg font-bold mb-4">Add Employee</h3>
                            <form id="addEmployeeForm">
                                @csrf
                                <input type="text" name="name" placeholder="Name" class="border p-2 w-full mb-2" required>
                                <input type="email" name="email" placeholder="Email" class="border p-2 w-full mb-2" required>
								<input type="text" name="employee_id" placeholder="Employee ID" class="border p-2 w-full mb-2" required>
                                <input type="text" name="phone" placeholder="Phone" class="border p-2 w-full mb-2">
                                <input type="text" name="position" placeholder="Position" class="border p-2 w-full mb-2">
                                <input type="text" name="pan_number" placeholder="PAN Number" class="border p-2 w-full mb-2">
								<input type="date" name="joining_date" placeholder="Joining Date" class="border p-2 w-full mb-2">
                                <textarea name="address" placeholder="Address" class="border p-2 w-full mb-2"></textarea>
                                <div class="flex justify-end">
                                    <button type="button" id="closeModal" class="bg-gray-400 text-white px-4 py-2 rounded mr-2">Cancel</button>
                                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Employee Table -->
                    <table id="employeesTable" class="display w-full mt-4">
                        <thead>
                            <tr>
                                <th style="display:none;">ID</th> <!-- Added ID Column -->
                                <th>Name</th>
                                <th>Email</th>
								<th>Employee ID</th>
                                <th>Phone</th>
                                <th>Position</th>
                                <th>PAN Number</th>
                                <th>Joining Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $emp)
                            <tr data-id="{{ $emp->id }}">
                                <td style="display:none;">{{ $emp->id }}</td> <!-- ID Cell -->
                                <td contenteditable="true" onBlur="updateField({{ $emp->id }}, 'name', this.innerText)">
                                    {{ $emp->name }}
                                </td>
                                <td contenteditable="true" onBlur="updateField({{ $emp->id }}, 'email', this.innerText)">
                                    {{ $emp->email }}
                                </td>
								<td contenteditable="true" onBlur="updateField({{ $emp->id }}, 'employee_id', this.innerText)">
                                    {{ $emp->employee_id }}
                                </td>
                                <td contenteditable="true" onBlur="updateField({{ $emp->id }}, 'phone', this.innerText)">
                                    {{ $emp->phone }}
                                </td>
                                <td contenteditable="true" onBlur="updateField({{ $emp->id }}, 'position', this.innerText)">
                                    {{ $emp->position }}
                                </td>
                                <td contenteditable="true" onBlur="updateField({{ $emp->id }}, 'pan_number', this.innerText)">
                                    {{ $emp->pan_number }}
                                </td>
                                <td contenteditable="true" onBlur="updateField({{ $emp->id }}, 'joining_date', this.innerText)">
                                    {{ $emp->joining_date }}
                                </td>
                                <td class="space-x-2">
                                    <!-- View Icon -->
                                    <a href="{{ route('employees.show', $emp->id) }}" class="text-blue-500 hover:text-blue-700" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Delete Icon -->
                                    <form method="POST" action="{{ route('employees.destroy', $emp->id) }}" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Delete" onclick="return confirm('Delete?')">
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
        // Initialize DataTable with ID DESC
        $('#employeesTable').DataTable({
			columnDefs: [
				{ targets: 0, visible: false, searchable: false }, // Hide ID column
				{ targets: -1, orderable: false } // Disable sorting for last column (Actions)
			],
            "order": [[0, "desc"]], // ID column, descending
            "pageLength": 10,
            "responsive": true
        });
    });

    // Open modal
    $('#openModal').click(function() {
        $('#addEmployeeModal').removeClass('hidden');
    });

    // Close modal
    $('#closeModal').click(function() {
        $('#addEmployeeModal').addClass('hidden');
    });

    // Submit add employee form via AJAX
    $('#addEmployeeForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('employees.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#addEmployeeModal').addClass('hidden');

                // Add new row to DataTable dynamically (insert at top)
                var table = $('#employeesTable').DataTable();
				table.row.add([
					`${response.id}`, // ID is hidden by DataTables configuration
					`<td contenteditable="true" onBlur="updateField(${response.id}, 'name', this.innerText)">${response.name}</td>`,
					`<td contenteditable="true" onBlur="updateField(${response.id}, 'email', this.innerText)">${response.email}</td>`,
					`<td contenteditable="true" onBlur="updateField(${response.id}, 'employee_id', this.innerText)">${response.employee_id}</td>`,
					`<td contenteditable="true" onBlur="updateField(${response.id}, 'phone', this.innerText)">${response.phone ?? ''}</td>`,
					`<td contenteditable="true" onBlur="updateField(${response.id}, 'position', this.innerText)">${response.position ?? ''}</td>`,
					`<td contenteditable="true" onBlur="updateField(${response.id}, 'pan_number', this.innerText)">${response.pan_number ?? ''}</td>`,
					`<td contenteditable="true" onBlur="updateField(${response.id}, 'joining_date', this.innerText)">${response.joining_date ?? ''}</td>`,
					`<td>
						<a href="/employees/${response.id}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-eye"></i></a>
						<form method="POST" action="/employees/${response.id}" style="display:inline">
							@csrf @method('DELETE')
							<button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Delete?')">
								<i class="fas fa-trash-alt"></i>
							</button>
						</form>
					</td>`
				]).order([0, 'desc']).draw(false); // Force reorder by ID DESC

                $('#addEmployeeForm')[0].reset();
            }
        });
    });

    // Inline update
    function updateField(id, field, value) {
        $.post(`/employees/${id}/inline-update`, {
            _token: '{{ csrf_token() }}',
            [field]: value
        });
    }
    </script>
</x-app-layout>