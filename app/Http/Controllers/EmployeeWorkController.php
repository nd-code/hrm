<?php

namespace App\Http\Controllers;

use App\Models\WorkSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
	
	public function workIndex()
    {
        $sessions = WorkSession::where('employee_id', auth('employee')->id())
			->orderBy('id', 'desc')
			->get();

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
}
