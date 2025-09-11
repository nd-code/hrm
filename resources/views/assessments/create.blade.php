<x-app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
			
				<h3 class="text-lg font-bold mb-4">New Performance Assessment</h3>

				<form method="POST" action="{{ route('assessments.store') }}">
					@csrf
					
					<div class="row mb-3">
						<div class="col-lg-3">
							<label>Employee</label>
							<select name="employee_id" class="form-control" required>
								<option value="">-- Select Employee --</option>
								@foreach($employees as $emp)
									<option value="{{ $emp->id }}">{{ $emp->name }}</option>
								@endforeach
							</select>
						</div>

						<div class="col-lg-3">
							<label>Date</label>
							<input type="date" name="assessment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
						</div>
					</div>

					@include('assessments.partials.form-fields')

				</form>

			</div>
        </div>
    </div>
</x-app-layout>