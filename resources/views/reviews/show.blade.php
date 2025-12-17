<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Feedback Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <h3 class="text-lg font-bold mb-4">Feedback Information</h3>

                <table class="w-full border-collapse border border-gray-300">
					<tr>
						<th class="border p-2 text-left">Employee</th>
						<td class="border p-2">{{ $review->employee->name }}</td>
					</tr>
					<tr>
						<th class="border p-2 text-left">Project</th>
						<td class="border p-2">
							<span class="editable"
								  data-field="project_name"
								  data-id="{{ $review->id }}">
								{{ $review->project_name }}
							</span>
						</td>
					</tr>
					<tr>
						<th class="border p-2 text-left">From</th>
						<td class="border p-2">
							<input type="date"
								   class="editable-date"
								   data-field="date_from"
								   data-id="{{ $review->id }}"
								   value="{{ $review->date_from }}">
						</td>
					</tr>
					<tr>
						<th class="border p-2 text-left">To</th>
						<td class="border p-2">
							<input type="date"
								   class="editable-date"
								   data-field="date_to"
								   data-id="{{ $review->id }}"
								   value="{{ $review->date_to }}">
						</td>
					</tr>
					<tr>
						<th class="border p-2 text-left">Feedback Given By</th>
						<td class="border p-2">
							<span class="editable"
								  data-field="review_given_by"
								  data-id="{{ $review->id }}">
								{{ $review->review_given_by }}
							</span>
						</td>
					</tr>
					<tr>
						<th class="border p-2 text-left">Feedback</th>
						<td class="border p-2">
							<span class="editable-textarea"
								  data-field="review"
								  data-id="{{ $review->id }}">
								{{ $review->review }}
							</span>
						</td>
					</tr>
					<tr>
						<th class="border p-2 text-left">Added At</th>
						<td class="border p-2">{{ $review->created_at->format('d-m-Y') }}</td>
					</tr>
				</table>

                <div class="mt-4">
                    <a href="@if(Auth::user()->id === 101){{ route('reviews.index') }}@else /employee/reviews-list @endif" class="bg-gray-600 text-white px-4 py-2 rounded">Back</a>
                </div>

            </div>
        </div>
    </div>
	
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', () => {
			document.querySelectorAll('.editable').forEach(makeInlineEditable);
			document.querySelectorAll('.editable-textarea').forEach(makeInlineTextareaEditable);
			document.querySelectorAll('.editable-date').forEach(input => {
				input.addEventListener('change', () => updateField(input.dataset.id, input.dataset.field, input.value));
			});
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
				const currentValue = element.textContent.trim();
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
			if (newValue !== oldValue) {
				fetch(`/reviews/${id}/inline-update`, {
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
						originalElement.textContent = newValue;
					} else {
						alert('Update failed!');
						inputElement.replaceWith(originalElement);
					}
				});
			} else {
				inputElement.replaceWith(originalElement);
			}
		}

		function updateField(id, field, value) {
			$.post(`/reviews/${id}/inline-update`, {
				_token: '{{ csrf_token() }}',
				[field]: value
			});
		}
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