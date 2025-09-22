<x-app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h3 class="text-lg font-bold mb-4">Vendors List</h3>

                    <!-- Add Vendor Button -->
                    <div class="flex justify-end mb-4">
                        <button id="openVendorModal" class="bg-blue-500 text-white px-4 py-2 rounded">
                            Add Vendor
                        </button>
                    </div>

                    <!-- Add Vendor Modal -->
                    <div id="addVendorModal" class="fixed inset-0 hidden bg-gray-800 bg-opacity-50 flex items-center justify-center" style="z-index: 1;">
                        <div class="bg-white rounded-lg p-6 w-1/3">
                            <h3 class="text-lg font-bold mb-4">Add Vendor</h3>
                            <form id="addVendorForm">
                                @csrf
                                <input type="text" name="name" placeholder="Name" class="border p-2 w-full mb-2" required>
                                <input type="text" name="contact_number" placeholder="Contact Number" class="border p-2 w-full mb-2">

                                <!-- Category Dropdown -->
                                <select name="category" class="border p-2 w-full mb-2" required>
                                    <option value="">-- Select Category --</option>
                                    <option value="Electrician">Electrician</option>
                                    <option value="Plumber">Plumber</option>
                                    <option value="AC Repair">AC Repair</option>
                                    <option value="Carpenter">Carpenter</option>
                                    <option value="Painter">Painter</option>
                                </select>

                                <input type="date" name="date" class="border p-2 w-full mb-2">
                                <textarea name="remark" placeholder="Remark" class="border p-2 w-full mb-2"></textarea>

                                <div class="flex justify-end">
                                    <button type="button" id="closeVendorModal" class="bg-gray-400 text-white px-4 py-2 rounded mr-2">Cancel</button>
                                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded savebtn">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Vendor Table -->
                    <table id="vendorsTable" class="display w-full mt-4">
                        <thead>
                            <tr>
                                <th style="display:none;">ID</th> <!-- Hidden ID -->
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Category</th>
                                <th>Date</th>
                                <th>Remark</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendors as $vendor)
                            <tr data-id="{{ $vendor->id }}">
                                <td style="display:none;">{{ $vendor->id }}</td>
                                <td contenteditable="true" onBlur="updateVendorField({{ $vendor->id }}, 'name', this.innerText)">
                                    {{ $vendor->name }}
                                </td>
                                <td contenteditable="true" onBlur="updateVendorField({{ $vendor->id }}, 'contact_number', this.innerText)">
                                    {{ $vendor->contact_number }}
                                </td>
                                <td contenteditable="true" onBlur="updateVendorField({{ $vendor->id }}, 'category', this.innerText)">
                                    {{ $vendor->category }}
                                </td>
                                <td>
                                    <input
                                        type="date"
                                        class="date-input"
                                        value="{{ $vendor->date }}"
                                        onchange="updateVendorField({{ $vendor->id }}, 'date', this.value)"
                                    />
                                </td>
                                <td contenteditable="true" onBlur="updateVendorField({{ $vendor->id }}, 'remark', this.innerText)">
                                    {{ $vendor->remark }}
                                </td>
                                <td class="space-x-2">
                                    <!-- View Icon (optional) -->
                                    <a href="{{ route('vendors.show', $vendor->id) }}" class="text-blue-500 hover:text-blue-700" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Delete Icon -->
                                    <form method="POST" action="{{ route('vendors.destroy', $vendor->id) }}" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Delete" onclick="return confirm('Delete this vendor?')">
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
        // Initialize DataTable
        $('#vendorsTable').DataTable({
            columnDefs: [
                { targets: 0, visible: false, searchable: false }, // hide ID column
                { targets: -1, orderable: false } // disable sorting for actions
            ],
            "order": [[0, "desc"]],
            "pageLength": 10,
            "responsive": true
        });
    });

    // Open modal
    $('#openVendorModal').click(function() {
        $('#addVendorModal').removeClass('hidden');
    });

    // Close modal
    $('#closeVendorModal').click(function() {
        $('#addVendorModal').addClass('hidden');
    });

    // Submit add vendor form via AJAX
    $('#addVendorForm').submit(function(e) {
        e.preventDefault();
        $('.savebtn').prop('disabled', true);
        $.ajax({
            url: "{{ route('vendors.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#addVendorModal').addClass('hidden');

                var table = $('#vendorsTable').DataTable();
                table.row.add([
                    `${response.id}`,
                    `<td contenteditable="true" onBlur="updateVendorField(${response.id}, 'name', this.innerText)">${response.name}</td>`,
                    `<td contenteditable="true" onBlur="updateVendorField(${response.id}, 'contact_number', this.innerText)">${response.contact_number ?? ''}</td>`,
                    `<td contenteditable="true" onBlur="updateVendorField(${response.id}, 'category', this.innerText)">${response.category}</td>`,
                    `<td><input type="date" class="date-input" value="${response.date ?? ''}" onchange="updateVendorField(${response.id}, 'date', this.value)" /></td>`,
                    `<td contenteditable="true" onBlur="updateVendorField(${response.id}, 'remark', this.innerText)">${response.remark ?? ''}</td>`,
                    `<td class="space-x-2">
                        <a href="/vendors/${response.id}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-eye"></i></a>
                        <form method="POST" action="/vendors/${response.id}" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Delete this vendor?')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>`
                ]).order([0, 'desc']).draw(false);

                $('#addVendorForm')[0].reset();
                $('.savebtn').prop('disabled', false);
            }
        });
    });

    // Inline update
    function updateVendorField(id, field, value) {
        $.post(`/vendors/${id}/inline-update`, {
            _token: '{{ csrf_token() }}',
            [field]: value
        });
    }
    </script>

    <style>
        .date-input {
            border: none;
            background: transparent;
            font: inherit;
            cursor: pointer;
            outline: none;
        }
    </style>
</x-app-layout>