<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Notifications\EmployeeNotification;

class NotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:255',
        ]);

        // Notify employees
        $employees = Employee::all();
        foreach ($employees as $employee) {
            $employee->notify(new \App\Notifications\EmployeeNotification($request->message));
        }

        // If it's AJAX, return JSON
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Notification sent']);
        }

        // Fallback (if not AJAX)
        return back()->with('success', 'Notification sent');
    }
}