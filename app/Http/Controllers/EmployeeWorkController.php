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
            // Start work
            WorkSession::create([
                'employee_id' => auth('employee')->id(),
                'work_date' => $today,
                'start_time' => now(),
            ]);
            $message = 'Work started!';
        } else {
            // Stop work
            $session->update([
                'end_time' => now(),
            ]);
            $message = 'Work stopped!';
        }

        return redirect()->back()->with('success', $message);
    }
}
