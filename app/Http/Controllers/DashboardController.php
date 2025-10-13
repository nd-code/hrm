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
        $yesterday = $now->copy()->subDay()->startOfDay();

        $onlineEmployees = WorkSession::with('employee')
            ->whereNull('end_time')   // means timer still running
            ->latest()
            ->get();
        
        $notifications = DB::table('notifications')
            ->selectRaw('data, MAX(created_at) as created_at')
            ->groupBy('data')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
                
        $today = Carbon::today();

        // Employees on leave today
        $employeesOnLeave = Leave::with('employee')
            ->where('from_date', '<=', $today)
            ->where('to_date', '>=', $today)
            ->where('status', 'Approved')
            ->get();

        return view('dashboard', compact('employeeCount', 'reviewCount', 'leaveCount', 'assessmentCount', 'onlineEmployees', 'notifications', 'employeesOnLeave'));
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
