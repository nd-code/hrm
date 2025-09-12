<x-app-layout>
	<style>
		tr.even td{
			background-color: #f9fafb;
		}
		tr.odd td{
			background-color: #ffffff;
		}
	</style>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

				<h3 class="text-lg font-bold mb-4">Performance Assessments</h3>

                <!-- Add New Assessment Button -->
                <div class="flex justify-end mb-4">
                    <a href="{{ route('assessments.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Add Assessment</a>
                </div>

                <!-- Assessment Table -->
                <table class="w-full table">
					<thead>
						<tr>
							<th style="display:none;">ID</th>
							<th>Employee</th>
							<th>Reviewer</th>
							<th>Assessment Date</th>
							<th>Final Conclusion</th>
							<th width="200">Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($assessments as $assessment)
						<tr class="{{ $loop->even ? 'even' : 'odd' }}">
							<td class="border-bottom-0" style="display:none;">{{ $assessment->id }}</td>
							<td class="border-bottom-0">{{ $assessment->employee->name ?? '-' }}</td>
							<td class="border-bottom-0">{{ $assessment->reviewer->name ?? '-' }}</td>
							<td class="border-bottom-0">{{ \Carbon\Carbon::parse($assessment->assessment_date)->format('d-m-Y') }}</td>
							<td class="border-bottom-0">{{ $assessment->final_conclusion ?? '-' }}</td>
							<td class="border-bottom-0">
								<a href="{{ route('assessments.show', $assessment) }}"><i class="fas fa-eye text-blue-500 mr-2"></i></a>
								<a href="{{ route('assessments.edit', $assessment) }}"><i class="fas fa-pencil text-blue-500 mr-2"></i></a>
								<form action="{{ route('assessments.destroy', $assessment) }}" method="POST" style="display:inline-block;">
									@csrf @method('DELETE')
									<button onclick="return confirm('Delete?')"><i class="fas fa-trash-alt text-red-500"></i></button>
								</form>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>

				{{ $assessments->links() }}
            </div>
        </div>
    </div>
	
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</x-app-layout>