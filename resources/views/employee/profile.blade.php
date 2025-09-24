<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $employee->name }}'s Info
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <h3 class="text-lg font-bold mb-4">My Details</h3>

                <table class="w-full border-collapse border border-gray-300 mb-4">
                    @foreach (['name', 'email', 'employee_id', 'phone', 'position', 'pan_number', 'address', 'joining_date', 'bank_details'] as $field)
                        <tr>
                            <th class="border p-2 text-left capitalize">{{ str_replace('_', ' ', $field) }}</th>
                            <td class="border p-2">
                                @if($field === 'joining_date' && !empty($employee->$field))
                                    {{ \Carbon\Carbon::parse($employee->$field)->format('d-m-Y') }}
                                @else
                                    {{ $employee->$field }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
					<!--<tr>
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
						<td class="border p-2"><a target="_blank" href="{{ route('employees.relieving-letter', $employee->id) }}"><button class="bg-blue-500 text-white px-4 py-2 rounded">Print</button></a></td>
					</tr>-->
                </table>

                <!--<a href="{{ route('employees.index') }}" 
                   class="inline-block bg-gray-600 text-white px-4 py-2 rounded">
                   ← Back
                </a>-->

            </div>
        </div>
    </div>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
</x-app-layout>