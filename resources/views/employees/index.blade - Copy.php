<x-app-layout>
	<x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employees') }}
        </h2>
    </x-slot>
	<div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
					<form action="{{ route('employees.store') }}" method="POST">
						@csrf
						<input type="text" name="name" placeholder="Name" required>
						<input type="email" name="email" placeholder="Email" required>
						<input type="text" name="phone" placeholder="Phone">
						<input type="text" name="position" placeholder="Position">
						<button type="submit">Add</button>
					</form>

					<table border="1" cellpadding="10">
						<thead>
							<tr><th>Name</th><th>Email</th><th>Phone</th><th>Position</th><th>Actions</th></tr>
						</thead>
						<tbody>
							@foreach($employees as $emp)
							<tr>
								<td contenteditable="true" onBlur="updateField({{ $emp->id }}, 'name', this.innerText)">
									{{ $emp->name }}
								</td>
								<td contenteditable="true" onBlur="updateField({{ $emp->id }}, 'email', this.innerText)">
									{{ $emp->email }}
								</td>
								<td contenteditable="true" onBlur="updateField({{ $emp->id }}, 'phone', this.innerText)">
									{{ $emp->phone }}
								</td>
								<td contenteditable="true" onBlur="updateField({{ $emp->id }}, 'position', this.innerText)">
									{{ $emp->position }}
								</td>
								<td>
									<a href="{{ route('employees.show', $emp->id) }}">View</a>
									<form method="POST" action="{{ route('employees.destroy', $emp->id) }}" style="display:inline">
										@csrf @method('DELETE')
										<button onclick="return confirm('Delete?')">Delete</button>
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

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
	function updateField(id, field, value) {
		$.post(`/employees/${id}/inline-update`, {
			_token: '{{ csrf_token() }}',
			[field]: value
		});
	}
	</script>
</x-app-layout>