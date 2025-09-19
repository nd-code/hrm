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
		$onlineEmployees = WorkSession::with('employee')
			->whereDate('work_date', today())
			->latest()
			->get();

		return view('online-employees', compact('onlineEmployees'));
	}
}
