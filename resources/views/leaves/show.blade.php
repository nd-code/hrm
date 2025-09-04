@php
	use Carbon\Carbon;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Leave Details
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">

                <h3 class="text-lg font-bold mb-4">Leave Information</h3>

                <table class="w-full border-collapse border border-gray-300 mb-4">
					<tr>
						<th class="border p-2 text-left">Employee</th>
						<td class="border p-2">{{ $leave->employee->name }}</td>
					</tr>
					<tr>
						<th class="border p-2 text-left">Apply To</th>
						<td class="border p-2">
							@php
								$ids = explode(',', $leave->apply_to ?? '');
								$names = \App\Models\Employee::whereIn('id', $ids)->pluck('name')->toArray();
							@endphp
							{{ implode(', ', $names) }}
						</td>
					</tr>
					<tr>
						<th class="border p-2 text-left">Leave Type</th>
						<td class="border p-2">
							<span class="editable"
								  data-field="leave_type"
								  data-id="{{ $leave->id }}">
								{{ $leave->leave_type }}
							</span>
						</td>
					</tr>
					<tr>
						<th class="border p-2 text-left">From</th>
						<td class="border p-2">
							<input type="date"
								   class="leave-date-input"
								   value="{{ $leave->from_date }}"
								   onchange="updateLeaveField({{ $leave->id }}, 'from_date', this.value)" />
						</td>
					</tr>
					<tr>
						<th class="border p-2 text-left">To</th>
						<td class="border p-2">
							<input type="date"
								   class="leave-date-input"
								   value="{{ $leave->to_date }}"
								   onchange="updateLeaveField({{ $leave->id }}, 'to_date', this.value)" />
						</td>
					</tr>
					<tr>
						<th class="border p-2 text-left">Number of Days</th>
						<td class="border p-2" id="leave-days">
							@php
								$fromDate = Carbon::parse($leave->from_date);
								$toDate = Carbon::parse($leave->to_date);
								$days = $fromDate->diffInDays($toDate) + 1;
							@endphp
							{{ $days }} Days
						</td>
					</tr>
					<tr>
						<th class="border p-2 text-left">Reason</th>
						<td class="border p-2">
							<span class="editable"
								  data-field="reason"
								  data-id="{{ $leave->id }}">
								{{ $leave->reason ?: 'Add here....' }}
							</span>
						</td>
					</tr>
					<tr>
						<th class="border p-2 text-left">Managed By</th>
						<td class="border p-2">{{ $leave->manager?->name ?? '-' }}</td>
					</tr>
					<tr>
						<th class="border p-2 text-left">Status</th>
						<td class="border p-2">{{ $leave->status }}</td>
					</tr>
				</table>

                <a href="{{ route('leaves.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded">← Back</a>

            </div>
        </div>
    </div>
	
	<script>
		document.addEventListener('DOMContentLoaded', () => {
			document.querySelectorAll('.editable').forEach(makeLeaveInlineEditable);
		});

		function makeLeaveInlineEditable(element) {
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

				input.addEventListener('blur', () => saveLeaveInlineEdit(input, element, id, field, currentValue));
			});
		}

		function saveLeaveInlineEdit(inputElement, originalElement, id, field, oldValue) {
			const newValue = inputElement.value.trim();
			const displayValue = newValue || 'Add here....';
			if (newValue !== oldValue) {
				fetch(`/leaves/${id}/inline-update`, {
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

		// Inline update for date pickers
		function updateLeaveField(id, field, value) {
			fetch(`/leaves/${id}/inline-update`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				body: JSON.stringify({ [field]: value })
			})
			.then(response => response.json())
			.then(data => {
				if (data.success && data.days !== undefined) {
					document.getElementById('leave-days').textContent = data.days + ' Days';
				}
			});
		}
	</script>
	
	<style>
		.leave-date-input {
			border: none;
			background: transparent;
			font: inherit;
			cursor: pointer;
		}

		.leave-date-input {
			outline: none;
		}
	</style>
</x-app-layout>