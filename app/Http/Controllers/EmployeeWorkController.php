<?php

namespace App\Http\Controllers;

use App\Models\WorkSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;
use App\Models\Employee;

class EmployeeWorkController extends Controller
{
    public function timerWork(Request $request)
    {
        $employeeId = auth('employee')->id();

        // Check if there is an open session (end_time is NULL)
        $openSession = WorkSession::where('employee_id', $employeeId)
            ->whereNull('end_time')
            ->latest()
            ->first();

        if ($openSession) {
            // Offline → close session
            $openSession->update([
                'end_time' => now(),
                'comment' => $request->comment
            ]);
            $message = 'Work stopped!';
        } else {
            // Online → start a new session (regardless of date rollover)
            WorkSession::create([
                'employee_id' => $employeeId,
                'work_date'   => today(),
                'start_time'  => now(),
            ]);
            $message = 'Work started!';
        }

        return redirect()->back()->with('success', $message);
    }
	
	public function workIndex(Request $request)
    {
        $employeeId = auth('employee')->id();

        $query = WorkSession::where('employee_id', $employeeId)
            ->orderBy('id', 'desc');

        // Apply filters only if provided
        if ($request->from_date) {
            $query->whereDate('work_date', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('work_date', '<=', $request->to_date);
        }

        $sessions = $query->get();

        return view('employee.work.index', compact('sessions'));
    }
	
	public function inlineUpdate(Request $request, $id)
	{
		$session = WorkSession::findOrFail($id);

		// allow only project_name or comment updates
		if (in_array($request->field, ['project_name', 'comment'])) {
			$session->{$request->field} = $request->value;
			$session->save();
		}

		return response()->json(['success' => true]);
	}
        
        public function exportPdf(Request $request)
        {
            $id = auth('employee')->id();
            
            $query = WorkSession::where('employee_id', $id);

            if ($request->from_date)
                $query->whereDate('work_date', '>=', $request->from_date);

            if ($request->to_date)
                $query->whereDate('work_date', '<=', $request->to_date);

            $data = $query->orderBy('id', 'desc')->get();

            $employee = Employee::find($id);

            $pdf = \PDF::loadView('pdf.attendances', compact('data', 'employee'))
                        ->setPaper('a4', 'portrait');

            return $pdf->download('attendance-report-' . now()->format('Y-m-d') . '.pdf');
        }
}
