@php
use App\Models\Position;
@endphp
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
                
                <div class="flex flex-col items-center mb-6">

                    <div class="relative group">
                        <img id="profilePreview"
                            src="{{ $employee->profile_photo 
                                    ? asset('storage/'.$employee->profile_photo) 
                                    : asset('images/default-avatar.png') }}"
                            class="w-32 h-32 rounded-full object-cover border shadow">

                        <!-- Change Button -->
                        <button onclick="document.getElementById('photoInput').click()"
                            class="absolute bottom-0 right-0 bg-blue-500 text-white p-2 rounded-full text-xs">
                            ✏️
                        </button>
                    </div>

                    <!-- Upload Form -->
                    <form id="photoForm"
                          action="{{ route('employee.upload-photo', $employee->id) }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="mt-3">
                        @csrf
                        <input type="file" name="profile_photo"
                               id="photoInput"
                               accept="image/*"
                               class="hidden"
                               onchange="previewAndSubmit(event)">
                    </form>

                    <!-- Delete Button (only if photo exists) -->
                    @if($employee->profile_photo)
                        <form action="{{ route('employee.delete-photo', $employee->id) }}"
                              method="POST"
                              class="mt-2"
                              onsubmit="return confirm('Are you sure you want to delete profile picture?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-red-600 text-sm hover:underline">
                                <i class="fa-solid fa-trash me-2 text-indigo-400"></i>
                            </button>
                        </form>
                    @endif

                </div>

                <table class="w-full border-collapse border border-gray-300 mb-4">
                    @foreach (['name', 'email', 'employee_id', 'phone', 'position', 'pan_number', 'address', 'joining_date', 'bank_details'] as $field)
                        <tr>
                            <th class="border p-2 text-left capitalize">{{ str_replace('_', ' ', $field) }}</th>
                            <td class="border p-2">
                                @if($field === 'joining_date' && !empty($employee->$field))
                                    {{ \Carbon\Carbon::parse($employee->$field)->format('d-m-Y') }}
                                @elseif ($field === 'position')
                                    @php
                                        $position = Position::find($employee->$field);
                                    @endphp
                                    {{ $position ? $position->name : '—' }}
                                @else
                                    {{ $employee->$field }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
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
				fetch(`/employee/${id}/inline-update`, {
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
			$.post(`/employee/${id}/inline-update`, {
				_token: '{{ csrf_token() }}',
				[field]: value
			});
		}
    </script>
        
        <script>
        function previewAndSubmit(event) {
            const reader = new FileReader();
            reader.onload = function(){
                document.getElementById('profilePreview').src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);

            document.getElementById('photoForm').submit();
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