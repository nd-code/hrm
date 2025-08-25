<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index()
	{
		return view('employees.index', [
			'employees' => Employee::orderBy('created_at', 'desc')->get()
		]);
	}

    public function store(Request $request)
	{
		$request->validate([
			'name' => 'required',
			'email' => 'required|email|unique:employees,email',
		]);

		$password = 'emp@123'; // Or use: Str::random(8);

		$employee = Employee::create([
			'name' => $request->name,
			'email' => $request->email,
			'phone' => $request->phone,
			'position' => $request->position,
			'pan_number' => $request->pan_number,
			'address' => $request->address,
			'password' => bcrypt($password),
		]);

		// Send login details to employee email
		//Mail::raw("Hi {$employee->name},\n\nYour account has been created.\nEmail: {$employee->email}\nPassword: {$password}\n\nLogin: " . url('/employee/login'), function ($msg) use ($employee) {
			//$msg->to($employee->email)->subject('Your Employee Account');
		//});

		// If it's an AJAX request, return JSON
		if ($request->ajax()) {
			return response()->json([
				'id' => $employee->id,
				'name' => $employee->name,
				'email' => $employee->email,
				'phone' => $employee->phone,
				'position' => $employee->position,
				'pan_number' => $employee->pan_number,
				'address' => $employee->address
			]);
		}

		// Fallback for normal requests
		return back()->with('success', 'Employee added and email sent.');
	}

    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return back()->with('success', 'Deleted.');
    }

    public function inlineUpdate(Request $request, Employee $employee)
    {
        $field = array_keys($request->except('_token'))[0];
        $employee->update([$field => $request->$field]);

        return response()->json(['success' => true]);
    }
}
