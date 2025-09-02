<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Review;
use App\Models\Leave;

class DashboardController extends Controller
{
    public function index()
    {
        $employeeCount = Employee::count();
        $reviewCount = Review::count();
		$leaveCount = Leave::count();

        return view('dashboard', compact('employeeCount', 'reviewCount', 'leaveCount'));
    }
}
