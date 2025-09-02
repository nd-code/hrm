<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::with('employee')->orderBy('id', 'desc')->get();
        $employees = Employee::orderBy('id', 'desc')->get();
        return view('leaves.index', compact('leaves', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|string|max:255',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'nullable|string',
        ]);

        $leave = Leave::create($request->all());

        // Load employee relation for AJAX response
        $leave->load('employee');

        return response()->json($leave);
    }

    public function show($id)
    {
        $leave = Leave::with('employee')->findOrFail($id);
        return view('leaves.show', compact('leave'));
    }

    public function update(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);
        $leave->update($request->only(['leave_type', 'from_date', 'to_date', 'reason', 'status']));
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $leave = Leave::findOrFail($id);
		$leave->delete();
        return back()->with('success', 'Leave deleted.');
    }
	
	public function updateStatus(Request $request, Leave $leave)
	{
		// Accept either lowercase or capitalized statuses from the UI
		$request->validate([
			'status' => 'required|string'
		]);

		$normalized = ucfirst(strtolower($request->status)); // approved -> Approved

		// Only allow the 3 valid values
		if (!in_array($normalized, ['Pending', 'Approved', 'Rejected'], true)) {
			return response()->json(['message' => 'Invalid status value.'], 422);
		}

		// (Optional) prevent changing once approved/rejected
		// if ($leave->status !== 'Pending') {
		//     return response()->json(['message' => 'Only pending leaves can be updated.'], 422);
		// }

		$leave->update(['status' => $normalized]);

		return response()->json([
			'success' => true,
			'status'  => $normalized,
			'id'      => $leave->id,
		]);
	}
	
	public function employeeIndex()
	{
		$leaves = Leave::where('employee_id', auth('employee')->id())->latest()->get();
		return view('employee.leaves.index', compact('leaves'));
	}

	public function employeeCreate()
	{
		return view('employee.leaves.create');
	}

	public function employeeStore(Request $request)
	{
		$validated = $request->validate([
			'leave_type' => 'required|string',
			'from_date' => 'required|date',
			'to_date' => 'required|date|after_or_equal:from_date',
			'reason' => 'nullable|string',
		]);

		$validated['employee_id'] = auth('employee')->id();
		$validated['status'] = 'Pending';

		$leave = Leave::create($validated);

		// Load employee relation for AJAX response
        $leave->load('employee');

        return response()->json($leave);
	}

	public function employeeShow(Leave $leave)
	{
		if ($leave->employee_id !== auth('employee')->id()) {
			abort(403, 'Unauthorized access.');
		}
		return view('employee.leaves.show', compact('leave'));
	}
}
