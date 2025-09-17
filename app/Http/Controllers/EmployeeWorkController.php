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
        $today = Carbon::today();

        // Find today's session
        $session = WorkSession::where('employee_id', auth('employee')->id())
            ->whereDate('work_date', $today)
            ->latest()
            ->first();

        if (!$session || $session->end_time) {
            // Online
            WorkSession::create([
                'employee_id' => auth('employee')->id(),
                'work_date' => $today,
                'start_time' => now(),
            ]);
            $message = 'Work started!';
        } else {
            // Offline
            $session->update([
                'end_time' => now(),
            ]);
            $message = 'Work stopped!';
        }

        return redirect()->back()->with('success', $message);
    }
	
	public function workIndex()
    {
        $sessions = WorkSession::where('employee_id', auth()->id())
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
