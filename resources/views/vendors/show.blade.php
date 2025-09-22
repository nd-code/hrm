<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Vendor Details
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">

            <h3 class="text-lg font-bold mb-4">Vendor Information</h3>

            <table class="w-full border-collapse border border-gray-300 mb-4">
                @foreach (['name', 'contact_number', 'category', 'date', 'remark'] as $field)
                    <tr>
                        <th class="border p-2 text-left capitalize">{{ str_replace('_', ' ', $field) }}</th>
                        <td class="border p-2">
                            @if ($field === 'date')
                                <input
                                    type="date"
                                    class="date-input"
                                    value="{{ $vendor->$field }}"
                                    onchange="updateField({{ $vendor->id }}, '{{ $field }}', this.value)"
                                />
                            @elseif ($field === 'remark')
                                <span class="editable-textarea"
                                      data-field="{{ $field }}"
                                      data-id="{{ $vendor->id }}">
                                      {{ $vendor->$field ?: 'Add here....' }}
                                </span>
                            @else
                                <span class="editable"
                                      data-field="{{ $field }}"
                                      data-id="{{ $vendor->id }}">
                                      {{ $vendor->$field }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>

            <a href="{{ route('vendors.index') }}" 
               class="inline-block bg-gray-600 text-white px-4 py-2 rounded">
               ← Back
            </a>
        </div>
    </div>

    <!-- Inline Editing Scripts -->
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
                if (currentValue === 'Add here....') currentValue = '';

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
            const displayValue = newValue || 'Add here....';
            if (newValue !== oldValue) {
                fetch(`/vendors/${id}/inline-update`, {
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

        function updateField(id, field, value) {
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