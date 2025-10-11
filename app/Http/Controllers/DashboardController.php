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
        
        $notifications = DB::table('notifications as n1')
            ->join(DB::raw('(SELECT data, MAX(created_at) as latest_created_at 
                             FROM notifications 
                             GROUP BY data) as n2'),
                function($join) {
                    $join->on('n1.data', '=', 'n2.data')
                         ->on('n1.created_at', '=', 'n2.latest_created_at');
                })
            ->select('n1.*')
            ->orderBy('n1.created_at', 'desc')
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
