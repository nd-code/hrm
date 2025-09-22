<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Review;
use App\Models\Leave;
use App\Models\Assessment;
use Carbon\Carbon;
use App\Models\WorkSession;

class DashboardController extends Controller
{
    public function index()
    {
        $employeeCount = Employee::count();
        $reviewCount = Review::count();
		$leaveCount = Leave::count();
		$assessmentCount = Assessment::count();
		
		$today = Carbon::today();
		$onlineEmployees = WorkSession::with('employee')
			->whereDate('work_date', $today)
			->get();

        return view('dashboard', compact('employeeCount', 'reviewCount', 'leaveCount', 'assessmentCount', 'onlineEmployees'));
    }
	
    public function getOnlineEmployees()
    {
        $now = Carbon::now();
        $yesterday = $now->copy()->subDay()->startOfDay();

        $onlineEmployees = WorkSession::with('employee')
            ->where(function ($q) use ($now, $yesterday) {
                // Sessions that started today
                $q->whereDate('work_date', $now->toDateString());

                // OR sessions that started yesterday but are still active past midnight
                $q->orWhere(function ($q2) use ($yesterday, $now) {
                    $q2->whereDate('work_date', $yesterday->toDateString())
                       ->whereNull('end_time')
                       ->orWhere('end_time', '>=', $yesterday->copy()->endOfDay());
                });
            })
            ->latest()
            ->get();

        return view('online-employees', compact('onlineEmployees'));
    }
}
