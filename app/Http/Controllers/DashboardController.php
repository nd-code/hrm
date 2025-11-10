<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Review;
use App\Models\Leave;
use App\Models\Assessment;
use Carbon\Carbon;
use App\Models\WorkSession;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $employeeCount = Employee::count();
        $reviewCount = Review::count();
        $leaveCount = Leave::count();
        $assessmentCount = Assessment::count();

        $now = Carbon::now();
        $today = Carbon::today();

        // Define your 24-hour window (9 PM → next day 9 PM)
        $displayStart = Carbon::today()->setTime(21, 0); // 9 PM today
        $displayEnd = (clone $displayStart)->addDay();   // 9 PM next day

        // Determine which date's leaves to show
        if ($now->lt($displayStart)) {
            // Before 9 PM today → show leaves from yesterday
            $leaveDate = Carbon::yesterday()->toDateString();
        } else {
            // After 9 PM today → show today's leaves
            $leaveDate = Carbon::today()->toDateString();
        }

        // Employees on leave (based on 9 PM → next day 9 PM logic)
        $employeesOnLeave = Leave::with('employee')
            ->where('from_date', '<=', $leaveDate)
            ->where('to_date', '>=', $leaveDate)
            ->where('status', 'Approved')
            ->orderBy('id', 'desc')
            ->get();

        // Online employees
        $onlineEmployees = WorkSession::with('employee')
            ->whereNull('end_time')   // means timer still running
            ->latest()
            ->get();

        // Notifications
        $notifications = DB::table('notifications')
            ->selectRaw('data, MAX(created_at) as created_at')
            ->groupBy('data')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'employeeCount',
            'reviewCount',
            'leaveCount',
            'assessmentCount',
            'onlineEmployees',
            'notifications',
            'employeesOnLeave'
        ));
    }
	
    public function getOnlineEmployees()
    {
        $now = Carbon::now();
        $yesterday = $now->copy()->subDay()->startOfDay();

        $onlineEmployees = WorkSession::with('employee')
            ->whereNull('end_time')   // means timer still running
            ->latest()
            ->get();

        return view('online-employees', compact('onlineEmployees'));
    }
}
