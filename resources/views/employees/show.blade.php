<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Employee Details
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">

            <!-- Tabs -->
            <ul id="tabs" class="flex border-b mb-6">
                <li class="-mb-px mr-1">
                    <a href="#tab-info"
                       class="tab-link bg-white inline-block border-l border-t border-r rounded-t py-2 px-4 font-semibold text-blue-600">
                       Employee Information
                    </a>
                </li>
                <li class="mr-1">
                    <a href="#tab-work"
                       class="tab-link bg-white inline-block py-2 px-4 text-gray-500 hover:text-blue-600">
                       Attendance
                    </a>
                </li>
            </ul>

            <!-- Tab Contents -->
            <div id="tab-contents">
                <!-- Employee Information Tab -->
                <div id="tab-info" class="tab-content p-4">
                    
                    <h3 class="text-lg font-bold mb-4">{{ $employee->name }}'s Info</h3>

                    <table class="w-full border-collapse border border-gray-300 mb-4">
                        @foreach (['name', 'email', 'employee_id', 'phone', 'position', 'pan_number', 'address', 'joining_date', 'bank_details'] as $field)
                            <tr>
                                <th class="border p-2 text-left capitalize">{{ str_replace('_', ' ', $field) }}</th>
                                <td class="border p-2">
                                    @if ($field === 'joining_date')
                                        <input
                                            type="date"
                                            class="joining-date-input"
                                            value="{{ $employee->$field }}"
                                            onchange="updateField({{ $employee->id }}, '{{ $field }}', this.value)"
                                        />
                                    @elseif ($field === 'bank_details')
                                        <span class="editable-textarea"
                                            data-field="{{ $field }}"
                                            data-id="{{ $employee->id }}">
                                            {{ $employee->$field ?: 'Add here....' }}
                                        </span>
                                    @else
                                        <span class="editable"
                                            data-field="{{ $field }}"
                                            data-id="{{ $employee->id }}">
                                            {{ $employee->$field }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <th class="border p-2 text-left">Documents</th>
                            <td class="border p-2">
                                <div id="documents-loading" class="hidden mb-2">
                                    <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    Uploading...
                                </div>
                                
                                <div id="documents-list" class="mb-2 text-sm">
                                    Loading...
                                </div>

                                <input type="file" id="document-upload" name="documents[]" multiple style="display: none;" />
                                <button type="button" onclick="document.getElementById('document-upload').click()"
                                    class="bg-blue-500 text-white px-3 py-1 rounded">
                                    Upload Document
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <th class="border p-2 text-left">Relieving Letter</th>
                            <td class="border p-2">
                                <a target="_blank" href="{{ route('employees.relieving-letter', $employee->id) }}">
                                    <button class="bg-blue-500 text-white px-4 py-2 rounded">Print</button>
                                </a>
                            </td>
                        </tr>
                    </table>

                </div>

                <!-- Attendance Tab -->
                <div id="tab-work" class="tab-content hidden p-4">
                    
                    <h3 class="text-lg font-bold mb-4">Attendance</h3>

                    <table id="workTable" class="w-full" style="width:100%;">
                        <thead>
                            <tr>
                                <th style="display:none;">Id</th>
                                <th>Date</th>
                                <th>Online</th>
                                <th>Offline</th>
                                <th>Total Hours</th>
                                <th>Project Name</th>
                                <th>Comment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
								@php
									$start = $session->start_time ? \Carbon\Carbon::parse($session->start_time) : null;
									$end = $session->end_time ? \Carbon\Carbon::parse($session->end_time) : null;
									$total = ($start && $end) ? $start->diff($end)->format('%H:%I:%S') : '-';
								@endphp
								<tr data-id="{{ $session->id }}">
									<td style="display:none;">{{ $session->id }}</td>
									<td>{{ \Carbon\Carbon::parse($session->work_date)->format('d-m-Y') }}</td>
									<td>
										{{ $start ? $start->format('h:i A') : '-' }}
									</td>
									<td>
										{{ $end ? $end->format('h:i A') : '-' }}
									</td>
									<td>{{ $total }}</td>
									<td>{{ $session->project_name }}</td>
									<td>{{ $session->comment }}</td>
								</tr>
							@endforeach
                        </tbody>
                    </table>

                </div>
				
				<a href="{{ route('employees.index') }}" 
				   class="inline-block bg-gray-600 text-white px-4 py-2 rounded">
				   ← Back
				</a>
            </div>

        </div>
    </div>

    <!-- Common Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Tab Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
			document.querySelectorAll('.editable').forEach(makeInlineEditable);
			document.querySelectorAll('.editable-textarea').forEach(makeInlineTextareaEditable);
		});

		function makeInlineEditable(element) {
			element.addEventListener('click', () => {
				const currentValue = element.textContent.trim();
				const field = element.dataset.field;
				const id = element.dataset.id;
				const input = document.createElement('input');

				input.type = 'text';
				input.value = currentValue;
				input.className = 'border p-1 w-full';
				element.replaceWith(input);
				input.focus();

				input.addEventListener('blur', () => saveInlineEdit(input, element, id, field, currentValue));
			});
		}

		function makeInlineTextareaEditable(element) {
			element.addEventListener('click', () => {
				let currentValue = element.textContent.trim();
				if (currentValue === 'Add here....') currentValue = ''; // Clear placeholder

				const field = element.dataset.field;
				const id = element.dataset.id;
				const textarea = document.createElement('textarea');

				textarea.className = 'border p-1 w-full';
				textarea.value = currentValue;
				textarea.rows = 3;
				element.replaceWith(textarea);
				textarea.focus();

				textarea.addEventListener('blur', () => saveInlineEdit(textarea, element, id, field, currentValue));
			});
		}

		function saveInlineEdit(inputElement, originalElement, id, field, oldValue) {
			const newValue = inputElement.value.trim();
			const displayValue = newValue || 'Add here....'; // Show placeholder if empty
			if (newValue !== oldValue) {
				fetch(`/employees/${id}/inline-update`, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					},
					body: JSON.stringify({ [field]: newValue })
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						inputElement.replaceWith(originalElement);
						originalElement.textContent = displayValue;
					} else {
						alert('Update failed!');
						inputElement.replaceWith(originalElement);
					}
				});
			} else {
				inputElement.replaceWith(originalElement);
			}
		}
		
		// Inline update
		function updateField(id, field, value) {
			$.post(`/employees/${id}/inline-update`, {
				_token: '{{ csrf_token() }}',
				[field]: value
			});
		}
		
		document.addEventListener('DOMContentLoaded', () => {
			loadEmployeeDocuments({{ $employee->id }});

			const fileInput = document.getElementById('document-upload');
			const loadingIndicator = document.getElementById('documents-loading');

			fileInput.addEventListener('change', function () {
				if (this.files.length === 0) return;

				loadingIndicator.classList.remove('hidden'); // Show loading
				const formData = new FormData();
				for (const file of this.files) {
					formData.append('documents[]', file);
				}

				fetch(`/employees/{{ $employee->id }}/upload-documents`, {
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					},
					body: formData
				})
				.then(response => response.json())
				.then(data => {
					loadingIndicator.classList.add('hidden'); // Hide loading
					if (data.success) {
						loadEmployeeDocuments({{ $employee->id }});
					} else {
						alert('Failed to upload files.');
					}
					fileInput.value = ''; // Reset input
				})
				.catch(() => {
					loadingIndicator.classList.add('hidden'); // Hide on error
					alert('An error occurred during upload.');
				});
			});
		});

		function loadEmployeeDocuments(employeeId) {
			fetch(`/employees/${employeeId}/documents`)
				.then(response => response.json())
				.then(data => {
					const container = document.getElementById('documents-list');
					container.innerHTML = '';
					if (data.documents.length === 0) {
						container.textContent = 'No documents uploaded.';
					} else {
						data.documents.forEach(doc => {
							const wrapper = document.createElement('div');
							wrapper.className = 'flex items-center justify-between mb-1';

							// File name (click to view in browser)
							const link = document.createElement('a');
							link.href = `/storage/${doc.file_path}`;
							link.textContent = doc.file_name;
							link.target = '_blank';
							link.className = 'text-blue-600 hover:underline flex-1';

							// Download button
							const downloadBtn = document.createElement('a');
							downloadBtn.href = `/storage/${doc.file_path}`;
							downloadBtn.download = doc.file_name;
							downloadBtn.textContent = '⬇️';
							downloadBtn.className = 'ml-2 text-green-600 hover:text-green-800';
							downloadBtn.style.cursor = 'pointer';

							// Delete button
							const deleteBtn = document.createElement('button');
							deleteBtn.textContent = '🗑️';
							deleteBtn.className = 'ml-2 text-red-600 hover:text-red-800';
							deleteBtn.style.cursor = 'pointer';
							deleteBtn.addEventListener('click', () => deleteDocument(employeeId, doc.id));

							wrapper.appendChild(link);
							wrapper.appendChild(downloadBtn); // Added before delete
							wrapper.appendChild(deleteBtn);
							container.appendChild(wrapper);
						});
					}
				});
		}

		function deleteDocument(employeeId, documentId) {
			if (!confirm('Are you sure you want to delete this document?')) return;

			fetch(`/employees/${employeeId}/documents/${documentId}`, {
				method: 'DELETE',
				headers: {
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				}
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					loadEmployeeDocuments(employeeId);
				} else {
					alert('Failed to delete document.');
				}
			});
		}
    </script>
	
	<style>
		.joining-date-input {
			border: none;
			background: transparent;
			font: inherit;
			cursor: pointer;
		}

		.joining-date-input {
			outline: none;
		}
	</style>

    <!-- Work Table Scripts -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#workTable').DataTable({
                columnDefs: [{ targets: 0, visible: false, searchable: false }],
                order: [[0, "desc"]],
                pageLength: 10,
                responsive: true,
                rowCallback: function (row, data, displayIndex) {
                    $(row).removeClass('odd even');
                    $(row).addClass(displayIndex % 2 === 0 ? 'even' : 'odd');
                }
            });
        });
    </script>

    <!-- Styles -->
    <style>
        #workTable tr.odd { background-color: #f9fafb; }
        #workTable tr.even { background-color: #ffffff; }
    </style>
</x-app-layout>