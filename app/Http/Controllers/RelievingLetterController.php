<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\RelievingLetter;
use Illuminate\Http\Request;

class RelievingLetterController extends Controller
{
    public function show($id)
	{
		$employee = Employee::findOrFail($id);

		$letter = \App\Models\RelievingLetter::where('employee_id', $employee->id)->first();

		return view('employees.relieving', compact('employee', 'letter'));
	}

    public function save(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        RelievingLetter::updateOrCreate(
            ['employee_id' => $employee->id],
            ['content' => $request->input('relieving_letter')]
        );

        return response()->json(['success' => true]);
    }
}
