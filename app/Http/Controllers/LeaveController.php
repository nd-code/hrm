<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
	{
		$query = Leave::with(['employee', 'manager'])->orderBy('id', 'desc');

		// Filters for AJAX request
		if ($request->ajax()) {
			if ($request->filled('employee_id')) {
				$query->where('employee_id', $request->employee_id);
			}
			if ($request->filled('from_date')) {
				$query->whereDate('from_date', '>=', $request->from_date);
			}
			if ($request->filled('to_date')) {
				$query->whereDate('to_date', '<=', $request->to_date);
			}

			$leaves = $query->get()->map(function ($leave) {
				// Convert apply_to IDs to employee names
				$applyToIds = explode(',', $leave->apply_to ?? '');
				$applyToNames = Employee::whereIn('id', $applyToIds)->pluck('name')->toArray();
				$leave->apply_to_names = implode(', ', $applyToNames);

				return $leave;
			});

			return response()->json([
				'data' => $leaves
			]);
		}

		$employees = Employee::orderBy('id', 'desc')->get();
		$leaves = Leave::with(['employee', 'manager'])->orderBy('id', 'desc')->get();

		return view('leaves.index', compact('employees', 'leaves'));
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
			'manage_by' => 'required|integer|exists:employees,id'
		]);

		$validated['employee_id'] = $request->employee_ids[0];
		$validated['status'] = 'Approved';
		
		// Convert array to comma-separated string
		if ($request->has('employee_ids')) {
			$validated['apply_to'] = implode(',', $request->employee_ids);
		}

		$leave = Leave::create($validated);
		
		// Send email to all selected employees
		/*if (!empty($request->employee_ids)) {
			$employees = Employee::whereIn('id', $request->employee_ids)->get();
			foreach ($employees as $emp) {
				\Mail::raw("Hello {$emp->name},\n\nA new leave has been applied from {$validated['from_date']} to {$validated['to_date']}.\nLeave Type: {$validated['leave_type']}\nReason: {$validated['reason']}", function ($msg) use ($emp) {
					$msg->to($emp->email)
						->subject('New Leave Application Notification');
				});
			}
		}*/

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
		if(isset($leave->leave_type) && $leave->leave_type == 'Half Day Leave')
		{
			$days = '0.5';
		}
		else
		{
			if ($leave->from_date && $leave->to_date) {
				$days = \Carbon\Carbon::parse($leave->from_date)
					->diffInDays(\Carbon\Carbon::parse($leave->to_date)) + 1;
			}
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
		
		// Notify the employee who applied for leave
		if ($leave->employee) {
			\Mail::raw("Hello {$leave->employee->name},\n\nYour leave request from {$leave->from_date} to {$leave->to_date} has been {$normalized}.", function ($msg) use ($leave) {
				$msg->to($leave->employee->email)
					->subject('Leave Status Updated');
			});
		}

		// Notify all selected employees in 'apply_to'
		if (!empty($leave->apply_to)) {
			$employeeIds = explode(',', $leave->apply_to);
			$employees = Employee::whereIn('id', $employeeIds)->get();
			foreach ($employees as $emp) {
				\Mail::raw("Hello {$emp->name},\n\nThe leave request from {$leave->from_date} to {$leave->to_date} has been {$normalized}.", function ($msg) use ($emp) {
					$msg->to($emp->email)
						->subject('Leave Status Updated');
				});
			}
		}

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
		
		// Send email to all selected employees
		if (!empty($request->employee_ids)) {
			$employees = Employee::whereIn('id', $request->employee_ids)->get();
			foreach ($employees as $emp) {
				\Mail::raw("Hello {$emp->name},\n\nA new leave has been applied from {$validated['from_date']} to {$validated['to_date']}.\nLeave Type: {$validated['leave_type']}\nReason: {$validated['reason']}", function ($msg) use ($emp) {
					$msg->to($emp->email)
						->subject('New Leave Application Notification');
				});
			}
		}

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
		if(isset($leave->leave_type) && $leave->leave_type == 'Half Day Leave')
		{
			$days = '0.5';
		}
		else
		{
			if ($leave->from_date && $leave->to_date) {
				$days = \Carbon\Carbon::parse($leave->from_date)
					->diffInDays(\Carbon\Carbon::parse($leave->to_date)) + 1;
			}
		}

		return response()->json([
			'success' => true,
			'days' => $days
		]);
	}
}
