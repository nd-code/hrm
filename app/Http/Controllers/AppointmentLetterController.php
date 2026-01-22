<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\AppointmentLetter;
use Illuminate\Http\Request;
use App\Models\Position;

class AppointmentLetterController extends Controller
{
    public function show($id)
	{
		$employee = Employee::findOrFail($id);
                
                $position = Position::where('id', $employee->position)->first();

		$letter = \App\Models\AppointmentLetter::where('employee_id', $employee->id)->first();

		return view('employees.appointment', compact('employee', 'position', 'letter'));
	}

    public function save(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        AppointmentLetter::updateOrCreate(
            ['employee_id' => $employee->id],
            ['content' => $request->input('appointment_letter')]
        );

        return response()->json(['success' => true]);
    }
}
