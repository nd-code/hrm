<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Employee;
use App\Models\Leave;
use App\Services\RecycleBinService;

class TeamController extends Controller
{
    public function teamIndex()
    {
        $teamEmployees = Team::with('employee')
            ->where('parent_employee_id', auth('employee')->id())
            ->get();

        $existingIds = $teamEmployees->pluck('employee_id')->toArray();

        $employees = Employee::whereNotIn('id', $existingIds)
            ->where('id', '!=', auth('employee')->id())
            ->get();

        // Get leaves for team members
        $teamLeaves = Leave::with('employee')
            ->whereIn('employee_id', $existingIds)
            ->orderBy('id', 'desc')
            ->get();

        return view('employee.team.index', compact('teamEmployees', 'employees', 'teamLeaves'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'parent_employee_id' => 'required|exists:employees,id',
            'employee_id' => 'required|exists:employees,id',
        ]);

        $team = Team::create($request->only(['parent_employee_id', 'employee_id']));

        return response()->json([
            'id' => $team->id,
            'employee_name' => $team->employee->name,
            'employee_email' => $team->employee->email,
            'employee_phone' => $team->employee->phone
        ]);
    }

    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        
        RecycleBinService::delete($team, 'team');
        
        $team->delete();
        return back()->with('success', 'Deleted.');
    }
    
    public function approve($id)
    {
        $leave = Leave::findOrFail($id);
        $leave->status = 'approved';
        $leave->manage_by = auth('employee')->id();
        $leave->save();
        
        $employeeId = auth('employee')->id();
        $employee = Employee::find($employeeId);
        $name = $employee ? $employee->name : 'Unknown';
        
        // Notify the employee who applied for leave
        if ($leave->employee) {
            $fromDate = \Carbon\Carbon::parse($leave->from_date)->format('d M Y');
            $toDate   = \Carbon\Carbon::parse($leave->to_date)->format('d M Y');

            $message = "Hello {$leave->employee->name},\n\nYour leave request from {$fromDate} to {$toDate} has been Approved by {$name}.";
            if (!empty($_POST['comment'])) {
                $message .= "\n\nNote: ".$_POST['comment'];
            }

            \Mail::raw($message, function ($msg) use ($leave) {
                $msg->to($leave->employee->email)
                    ->subject('Leave Status Updated');
            });
        }

        // Notify all selected employees in 'apply_to'
        if (!empty($leave->apply_to)) {
            $employeeIds = explode(',', $leave->apply_to);
            $employees = Employee::whereIn('id', $employeeIds)->get();

            $fromDate = \Carbon\Carbon::parse($leave->from_date)->format('d M Y');
            $toDate   = \Carbon\Carbon::parse($leave->to_date)->format('d M Y');

            foreach ($employees as $emp) {
                \Mail::raw("Hello {$emp->name},\n\nThe leave request of {$leave->employee->name} from {$fromDate} to {$toDate} has been Approved by {$name}.", function ($msg) use ($emp) {
                    $msg->to($emp->email)
                        ->subject('Leave Status Updated');
                });
            }
        }

        return back()->with('success', 'Leave approved.');
    }

    public function reject($id)
    {
        $leave = Leave::findOrFail($id);
        $leave->status = 'rejected';
        $leave->manage_by = auth('employee')->id();
        $leave->save();
        
        $employeeId = auth('employee')->id();
        $employee = Employee::find($employeeId);
        $name = $employee ? $employee->name : 'Unknown';
        
        // Notify the employee who applied for leave
        if ($leave->employee) {
            $fromDate = \Carbon\Carbon::parse($leave->from_date)->format('d M Y');
            $toDate   = \Carbon\Carbon::parse($leave->to_date)->format('d M Y');

            $message = "Hello {$leave->employee->name},\n\nYour leave request from {$fromDate} to {$toDate} has been Rejected by {$name}.";
            if (!empty($_POST['comment'])) {
                $message .= "\n\nNote: ".$_POST['comment'];
            }

            \Mail::raw($message, function ($msg) use ($leave) {
                $msg->to($leave->employee->email)
                    ->subject('Leave Status Updated');
            });
        }

        // Notify all selected employees in 'apply_to'
        if (!empty($leave->apply_to)) {
            $employeeIds = explode(',', $leave->apply_to);
            $employees = Employee::whereIn('id', $employeeIds)->get();

            $fromDate = \Carbon\Carbon::parse($leave->from_date)->format('d M Y');
            $toDate   = \Carbon\Carbon::parse($leave->to_date)->format('d M Y');

            foreach ($employees as $emp) {
                \Mail::raw("Hello {$emp->name},\n\nThe leave request of {$leave->employee->name} from {$fromDate} to {$toDate} has been Rejected by {$name}.", function ($msg) use ($emp) {
                    $msg->to($emp->email)
                        ->subject('Leave Status Updated');
                });
            }
        }

        return back()->with('success', 'Leave rejected.');
    }
}