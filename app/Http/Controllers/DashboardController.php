<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Review;
use App\Models\Leave;
use App\Models\Assessment;

class DashboardController extends Controller
{
    public function index()
    {
        $employeeCount = Employee::count();
        $reviewCount = Review::count();
		$leaveCount = Leave::count();
		$assessmentCount = Assessment::count();

        return view('dashboard', compact('employeeCount', 'reviewCount', 'leaveCount', 'assessmentCount'));
    }
}
