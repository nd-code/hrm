<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
	{
		$leaves = Leave::with('employee')->latest()->get();
		$employees = Employee::all();

		return view('leaves.index', compact('leaves', 'employees'));
	}

    public function create()
    {
        $employees = User::where('role', 'Employee')->get();
        return view('leaves.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'leave_type' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'nullable|string',
        ]);

        Leave::create($validated);
        return redirect()->route('leaves.index')->with('success', 'Leave request submitted.');
    }

    public function edit(Leave $leave)
    {
        $employees = User::where('role', 'Employee')->get();
        return view('leaves.edit', compact('leave', 'employees'));
    }

    public function update(Request $request, Leave $leave)
    {
        $validated = $request->validate([
            'leave_type' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'nullable|string',
            'status' => 'required|in:Pending,Approved,Rejected'
        ]);

        $leave->update($validated);
        return redirect()->route('leaves.index')->with('success', 'Leave updated.');
    }

    public function destroy(Leave $leave)
    {
        $leave->delete();
        return redirect()->route('leaves.index')->with('success', 'Leave deleted.');
    }
}
