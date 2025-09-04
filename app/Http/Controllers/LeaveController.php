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
        $validated = $request->validate([
			'employee_ids' => 'nullable|array',
			'employee_ids.*' => 'exists:employees,id',
			'leave_type' => 'required|string',
			'from_date' => 'required|date',
			'to_date' => 'required|date|after_or_equal:from_date',
			'reason' => 'nullable|string',
		]);

		$validated['employee_id'] = $request->employee_ids[0];
		$validated['status'] = 'Pending';
		
		// Convert array to comma-separated string
		if ($request->has('employee_ids')) {
			$validated['apply_to'] = implode(',', $request->employee_ids);
		}

		$leave = Leave::create($validated);

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
		
		$days = null;
		if ($leave->from_date && $leave->to_date) {
			$days = \Carbon\Carbon::parse($leave->from_date)
				->diffInDays(\Carbon\Carbon::parse($leave->to_date)) + 1;
		}
		
        return response()->json(['success' => true, 'days' => $days]);
    }

    public function destroy($id)
    {
        $leave = Leave::findOrFail($id);
		$leave->delete();
        return back()->with('success', 'Leave deleted.');
    }
	
	public function updateStatus(Request $request, Leave $leave)
	{
		$request->validate([
			'status' => 'required|string',
			'manage_by' => 'required|integer|exists:employees,id',
		]);

		$normalized = ucfirst(strtolower($request->status));

		if (!in_array($normalized, ['Pending', 'Approved', 'Rejected'], true)) {
			return response()->json(['message' => 'Invalid status value.'], 422);
		}

		$leave->update([
			'status'    => $normalized,
			'manage_by' => $request->manage_by,
		]);

		return response()->json([
			'success' => true,
			'status'  => $normalized,
			'manager' => $leave->manager?->name ?? '',
			'id'      => $leave->id,
		]);
	}
	
	public function employeeIndex()
	{
		$leaves = Leave::where('employee_id', auth('employee')->id())->latest()->get();
		$employees = Employee::where('id', '!=', auth()->id())->orderBy('id', 'desc')->get();
		return view('employee.leaves.index', compact('leaves', 'employees'));
	}

	public function employeeCreate()
	{
		return view('employee.leaves.create');
	}

	public function employeeStore(Request $request)
	{
		$validated = $request->validate([
			'employee_ids' => 'nullable|array',
			'employee_ids.*' => 'exists:employees,id',
			'leave_type' => 'required|string',
			'from_date' => 'required|date',
			'to_date' => 'required|date|after_or_equal:from_date',
			'reason' => 'nullable|string',
		]);

		$validated['employee_id'] = auth('employee')->id();
		$validated['status'] = 'Pending';
		
		// Convert array to comma-separated string
		if ($request->has('employee_ids')) {
			$validated['apply_to'] = implode(',', $request->employee_ids);
		}

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
	
	public function inlineUpdate(Request $request, $id)
	{
		$leave = Leave::findOrFail($id);
		$leave->update($request->only(['leave_type', 'from_date', 'to_date', 'reason']));

		$days = null;
		if ($leave->from_date && $leave->to_date) {
			$days = \Carbon\Carbon::parse($leave->from_date)
				->diffInDays(\Carbon\Carbon::parse($leave->to_date)) + 1;
		}

		return response()->json([
			'success' => true,
			'days' => $days
		]);
	}
}
