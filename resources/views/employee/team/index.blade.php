@php
	use Carbon\Carbon;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Team') }}
        </h2>
    </x-slot>
    
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Tabs -->
                    <ul id="tabs" class="flex border-b mb-6">
                        <li class="-mb-px mr-1">
                            <a href="#tab-team"
                               class="tab-link bg-white inline-block border-l border-t border-r rounded-t py-2 px-4 font-semibold text-blue-600">
                               My Team
                            </a>
                        </li>
                        <li class="mr-1">
                            <a href="#tab-leaves"
                               class="tab-link bg-white inline-block py-2 px-4 text-gray-500 hover:text-blue-600">
                               Leaves
                            </a>
                        </li>
                    </ul>

                    <div id="tab-contents">
                        <!-- My Team Tab -->
                        <div id="tab-team" class="tab-content p-4">
                            <h3 class="text-lg font-bold mb-4">Team</h3>

                            <!-- Add Team Member Button -->
                            <div class="flex justify-end mb-4">
                                <button id="openModal" class="bg-blue-500 text-white px-4 py-2 rounded">
                                    Add Team Member
                                </button>
                            </div>

                            <!-- Add Team Member Modal -->
                            <div id="addTeamModal" class="fixed inset-0 hidden bg-gray-800 bg-opacity-50 flex items-center justify-center" style="z-index: 1;">
                                <div class="bg-white rounded-lg p-6 w-1/3">
                                    <h3 class="text-lg font-bold mb-4">Add Team Member</h3>
                                    <form id="addTeamForm">
                                        @csrf
                                        <input type="hidden" name="parent_employee_id" value="{{ auth()->id() }}">

                                        <label class="block mb-1 font-semibold">Select Employee:</label>
                                        <select name="employee_id" class="border p-2 w-full mb-4" required>
                                            <option value="">Select Employee</option>
                                            @foreach($employees as $emp)
                                                <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                            @endforeach
                                        </select>

                                        <div class="flex justify-end">
                                            <button type="button" id="closeModal" class="bg-gray-400 text-white px-4 py-2 rounded mr-2">Cancel</button>
                                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded savebtn">Add</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Team Employees Table -->
                            <table id="teamTable" class="display w-full mt-4">
                                <thead>
                                    <tr>
                                        <th style="display:none;">ID</th>
                                        <th>Employee Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($teamEmployees as $teamEmp)
                                    <tr data-id="{{ $teamEmp->id }}">
                                        <td style="display:none;">{{ $teamEmp->id }}</td>
                                        <td>{{ $teamEmp->employee->name }}</td>
                                        <td>{{ $teamEmp->employee->email }}</td>
                                        <td>{{ $teamEmp->employee->phone }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('employee.team.destroy', $teamEmp->id) }}" style="display:inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700" title="Remove Team Member" onclick="return confirm('Remove this member from team?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Leaves Tab -->
                        <div id="tab-leaves" class="tab-content hidden p-4">
                            <h3 class="text-lg font-bold mb-4">Team Leaves</h3>

                            <table id="leaveTable" class="display w-full">
                                <thead>
                                    <tr>
                                        <th style="display:none;">ID</th>
                                        <th>Employee</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Days</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($teamLeaves as $leave)
                                    <tr>
                                        <td style="display:none;">{{ $leave->id }}</td>
                                        <td>{{ $leave->employee->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($leave->from_date)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($leave->to_date)->format('d-m-Y') }}</td>
                                        <td>
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
                                        <td>{{ $leave->reason }}</td>
                                        <td>
                                            @if($leave->status === 'Pending')
                                                <form action="{{ route('employee.team.leave.approve', $leave->id) }}" 
                                                      method="POST" 
                                                      style="display:inline"
                                                      onsubmit="return confirm('Are you sure you want to APPROVE this leave request?')">
                                                    @csrf
                                                    <button type="submit" class="approve-btn text-green-500 ml-2 mr-2" title="Approve">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('employee.team.leave.reject', $leave->id) }}" 
                                                      method="POST" 
                                                      style="display:inline"
                                                      onsubmit="return confirm('Are you sure you want to REJECT this leave request?')">
                                                    @csrf
                                                    <button type="submit" class="reject-btn text-red-500 mr-2" title="Reject">
                                                        <i class="fas fa-times-circle"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span>{{ ucfirst($leave->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

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
        $('#teamTable').DataTable({
            columnDefs: [
                { targets: 0, visible: false, searchable: false },
                { targets: -1, orderable: false }
            ],
            order: [[0, "desc"]],
            pageLength: 10,
            responsive: true
        });
        
        $('#leaveTable').DataTable({
            columnDefs: [{ targets: 0, visible: false, searchable: false }],
            order: [[0, "desc"]],
            pageLength: 10,
            responsive: true
        });

        // Open modal
        $('#openModal').click(function() {
            $('#addTeamModal').removeClass('hidden');
        });

        // Close modal
        $('#closeModal').click(function() {
            $('#addTeamModal').addClass('hidden');
        });

        // Submit Add Team Member form via AJAX
        $('#addTeamForm').submit(function(e) {
            e.preventDefault();
            $('.savebtn').prop('disabled', true);

            $.ajax({
                url: "{{ route('employee.team.store') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#addTeamModal').addClass('hidden');
                    $('.savebtn').prop('disabled', false);

                    var table = $('#teamTable').DataTable();
                    table.row.add([
                        response.id,
                        response.employee_name,
                        response.employee_email,
                        response.employee_phone,
                        `<form method="POST" action="/employee/team/${response.id}" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Remove this member from team?')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>`
                    ]).order([0, 'desc']).draw(false);

                    $('#addTeamForm')[0].reset();
                    
                    location.reload();
                },
                error: function() {
                    alert('Something went wrong!');
                    $('.savebtn').prop('disabled', false);
                }
            });
        });

        // Tabs script (same as Employee Details)
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
    
    <!-- Styles -->
    <style>
        #leaveTable{width: 100% !important;}
    </style>
</x-app-layout>