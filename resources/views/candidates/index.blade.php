<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold">Candidate List</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
            
                    <h3 class="text-lg font-bold mb-4">Candidate List</h3>

                    <!-- Add Candidate Button -->
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('candidates.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
                             Add Candidate
                         </a>
                    </div>

                    <table id="candidatesTable" class="w-full">
                        <thead>
                            <tr>
                                <th style="display:none;">ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>City</th>
                                <th>Salary (Per Annum)</th>
                                <th>Exp. (Yrs.)</th>
                                <th>Designation</th>
                                <th>Interview Taken Date</th>
                                <th>Comment</th>
                                <th>Conclusion</th>
                                <th>CV</th>
                                <th>Created On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($candidates as $c)
                            <tr>
                                <td style="display:none;">{{ $c->id }}</td>
                                <td>
                                    <a href="{{ route('candidates.edit', $c->id) }}" class="hover:underline">
                                        {{ $c->name }}
                                    </a>
                                </td>

                                <td contenteditable="true" onBlur="updateField(this, '{{ $c->id }}', 'email')">
                                    {{ $c->email }}
                                </td>

                                <td contenteditable="true" onBlur="updateField(this, '{{ $c->id }}', 'phone')">
                                    {{ $c->phone }}
                                </td>

                                <td contenteditable="true" onBlur="updateField(this, '{{ $c->id }}', 'city')">
                                    {{ $c->city }}
                                </td>

                                <td contenteditable="true" onBlur="updateField(this, '{{ $c->id }}', 'salary')">
                                    {{ $c->salary }}
                                </td>

                                <td contenteditable="true" onBlur="updateField(this, '{{ $c->id }}', 'work_experience')">
                                    {{ $c->work_experience }}
                                </td>

                                <td contenteditable="true" onBlur="updateField(this, '{{ $c->id }}', 'designation')">
                                    {{ $c->designation }}
                                </td>

                                <td>
                                    <input type="date"
                                           class="border p-1 rounded interview-date"
                                           value="{{ $c->interview_date ? \Carbon\Carbon::parse($c->interview_date)->format('Y-m-d') : '' }}"
                                           onBlur="updateField(this, '{{ $c->id }}', 'interview_date')" />
                                </td>

                                <td contenteditable="true" onBlur="updateField(this, '{{ $c->id }}', 'comment')">
                                    {{ $c->comment }}
                                </td>

                                <td>
                                    <select onchange="updateFieldSelect(this, '{{ $c->id }}', 'status')"
                                            class="border p-1 rounded" style="width: 100px;">
                                        <option value="pending" {{ $c->status=='pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="on_hold"  {{ $c->status == 'on_hold'  ? 'selected' : '' }}>On Hold</option>
                                        <option value="selected" {{ $c->status=='selected' ? 'selected' : '' }}>Selected</option>
                                        <option value="rejected" {{ $c->status=='rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </td>

                                <td>
                                    @if($c->cv)
                                        <a href="{{ asset('storage/'.$c->cv) }}" target="_blank"
                                           class="text-blue-600 underline"><i class="fas fa-file-alt"></i></a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>{{ $c->created_at ? \Carbon\Carbon::parse($c->created_at)->format('d-m-Y') : '' }}</td>

                                <td>
                                    <a href="{{ route('candidates.edit', $c->id) }}" class="text-blue-600 mr-2"><i class="fas fa-pencil-alt"></i></a>

                                    <form action="{{ route('candidates.destroy', $c->id) }}"
                                          method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600"
                                            onclick="return confirm('Delete this candidate?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <br>
                    <a href="{{ route('setting') }}" 
                       class="inline-block bg-gray-600 text-white px-4 py-2 rounded">
                       ← Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <script>
        $('#candidatesTable').DataTable({
            columnDefs: [
                { targets: 0, visible: false, searchable: false }, // hide ID column
                { targets: -1, orderable: false } // disable sorting for actions
            ],
            "order": [[0, "desc"]],
            "pageLength": 10,
            "responsive": true
        });

        function updateField(el, id, field) {
            if(field == 'interview_date')
            {
                let value = el.value;

                $.post("{{ route('candidates.inline.update') }}", {
                    id: id,
                    field: field,
                    value: value,
                    _token: "{{ csrf_token() }}"
                });
            }
            else
            {
                $.post("{{ route('candidates.inline.update') }}", {
                    id: id,
                    field: field,
                    value: el.innerText,
                    _token: "{{ csrf_token() }}"
                });
            }
        }

        function updateFieldSelect(el, id, field) {
            $.post("{{ route('candidates.inline.update') }}", {
                id: id,
                field: field,
                value: el.value,
                _token: "{{ csrf_token() }}"
            });
        }
    </script>
</x-app-layout>