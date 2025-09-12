<x-app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
			
				<h3 class="text-lg font-bold mb-4">Edit Performance Assessment</h3>

				<form method="POST" action="{{ route('assessments.update', $assessment) }}">
					@csrf @method('PUT')

					<div class="row mb-3">
						<div class="col-lg-3">
							<label>Employee:</label>
							<select class="form-control" required disabled>
								@foreach($employees as $emp)
									<option value="{{ $emp->id }}" @selected($emp->id == $assessment->employee_id)>
										{{ $emp->name }}
									</option>
								@endforeach
							</select>
							
							<input type="hidden" name="employee_id" value="{{ $assessment->employee_id }}" />
						</div>

						<div class="col-lg-3">
							<label>Assessment Date:</label>
							<input type="date" name="assessment_date" class="form-control" value="{{ $assessment->assessment_date }}" required>
						</div>
					</div>

					@include('assessments.partials.form-fields',['assessment'=>$assessment])

				</form>

			</div>
        </div>
    </div>
</x-app-layout>