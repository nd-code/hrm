<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $employee->name }}'s Info
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <h3 class="text-lg font-bold mb-4">Employee Information</h3>

                <table class="w-full border-collapse border border-gray-300 mb-4">
                    @foreach (['name', 'email', 'employee_id', 'phone', 'position', 'pan_number', 'address', 'joining_date'] as $field)
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
                </table>

                <a href="{{ route('employees.index') }}" 
                   class="inline-block bg-gray-600 text-white px-4 py-2 rounded">
                   ← Back
                </a>

            </div>
        </div>
    </div>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.editable').forEach(element => {
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

                    input.addEventListener('blur', () => {
                        const newValue = input.value.trim();
                        if (newValue !== currentValue) {
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
                                    input.replaceWith(element);
                                    element.textContent = newValue;
                                } else {
                                    alert('Update failed!');
                                    input.replaceWith(element);
                                }
                            });
                        } else {
                            input.replaceWith(element);
                        }
                    });
                });
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