<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Review;
use App\Models\Leave;
use App\Models\Assessment;
use Carbon\Carbon;
use App\Models\WorkSession;
use Illuminate\Support\Facades\DB;
use App\Models\Holiday;

class DashboardController extends Controller
{
    public function index()
    {
        $employeeCount = Employee::count();
        $reviewCount = Review::count();
        $leaveCount = Leave::count();
        $assessmentCount = Assessment::count();

        $now = Carbon::now();

        // Define your 24-hour window (9 PM → next day 9 PM)
        $displayStart = Carbon::today()->setTime(21, 0);

        // Determine which date's leaves to show
        if ($now->lt($displayStart)) {
            $leaveDate = Carbon::yesterday()->toDateString();
        } else {
            $leaveDate = Carbon::today()->toDateString();
        }

        // Employees on leave
        $employeesOnLeave = Leave::with('employee')
            ->where('from_date', '<=', $leaveDate)
            ->where('to_date', '>=', $leaveDate)
            ->where('status', 'Approved')
            ->orderBy('id', 'desc')
            ->get();

        // Online employees
        $onlineEmployees = WorkSession::with('employee')
            ->whereNull('end_time')
            ->latest()
            ->get();

        // Notifications
        $notifications = DB::table('notifications')
            ->selectRaw('data, MAX(created_at) as created_at')
            ->groupBy('data')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Upcoming Holidays (Today + Next 2 Days)
        $upcomingHolidays = Holiday::whereBetween(
                'holiday_date',
                [
                    Carbon::today()->toDateString(),
                    Carbon::today()->addDays(2)->toDateString()
                ]
            )
            ->orderBy('holiday_date')
            ->get();

        return view('dashboard', compact(
            'employeeCount',
            'reviewCount',
            'leaveCount',
            'assessmentCount',
            'onlineEmployees',
            'notifications',
            'employeesOnLeave',
            'upcomingHolidays'
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
