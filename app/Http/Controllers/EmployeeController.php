<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\EmployeeDocument;
use Illuminate\Support\Facades\Storage;
use App\Models\WorkSession;

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
			'employee_id' => 'nullable|string|unique:employees,employee_id',
			'joining_date' => 'nullable|date'
		]);

		$password = Str::random(8);

		$employee = Employee::create([
			'name' => $request->name,
			'email' => $request->email,
			'employee_id' => $request->employee_id,
			'phone' => $request->phone,
			'position' => $request->position,
			'pan_number' => $request->pan_number,
			'joining_date' => $request->joining_date,
			'address' => $request->address,
			'password' => bcrypt($password),
		]);

		// Send login details to employee email
		Mail::raw("Hi {$employee->name},\n\nYour account has been created.\nEmail: {$employee->email}\nPassword: {$password}\n\nYou can login here:\n" . url('/employee/login'), function ($msg) use ($employee) {
			$msg->to($employee->email)->subject('Your Employee Account');
		});

		// If it's an AJAX request, return JSON
		if ($request->ajax()) {
			return response()->json([
				'id' => $employee->id,
				'name' => $employee->name,
				'email' => $employee->email,
				'employee_id' => $employee->employee_id,
				'phone' => $employee->phone,
				'position' => $employee->position,
				'pan_number' => $employee->pan_number,
				'joining_date' => $employee->joining_date
			]);
		}

		// Fallback for normal requests
		return back()->with('success', 'Employee added and email sent.');
	}

    public function show(Employee $employee)
    {
		$sessions = WorkSession::where('employee_id', $employee->id)
			->orderBy('id', 'desc')
			->get();

        return view('employees.show', compact('employee', 'sessions'));
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
	
	public function uploadDocuments(Request $request, $id)
	{
		$request->validate([
			'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // 5MB max
		]);

		$uploadedFiles = [];
		foreach ($request->file('documents', []) as $file) {
			$path = $file->store('employee_documents', 'public');
			$document = EmployeeDocument::create([
				'employee_id' => $id,
				'file_name' => $file->getClientOriginalName(),
				'file_path' => $path,
			]);
			$uploadedFiles[] = $document;
		}

		return response()->json(['success' => true, 'documents' => $uploadedFiles]);
	}

	public function getDocuments($id)
	{
		$documents = EmployeeDocument::where('employee_id', $id)->get();
		return response()->json(['success' => true, 'documents' => $documents]);
	}
	
	public function deleteDocument($employeeId, $documentId)
	{
		$document = EmployeeDocument::where('employee_id', $employeeId)->findOrFail($documentId);

		// Delete file from storage
		if (Storage::disk('public')->exists($document->file_path)) {
			Storage::disk('public')->delete($document->file_path);
		}

		// Delete record
		$document->delete();

		return response()->json(['success' => true]);
	}
	
	public function relievingLetter($id)
    {
        $employee = Employee::findOrFail($id);

        return view('employees.relieving', compact('employee'));
    }
}
