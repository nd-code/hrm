<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Review;

class DashboardController extends Controller
{
    public function index()
    {
        $employeeCount = Employee::count();
        $reviewCount = Review::count();

        return view('dashboard', compact('employeeCount', 'reviewCount'));
    }
}
