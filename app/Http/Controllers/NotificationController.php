<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Notifications\AdminToEmployeeNotification;
use App\Events\SendNotificationEvent;

class NotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:255',
        ]);
        
        $employees = Employee::all();

        foreach ($employees as $employee) {
            $employee->notify(new AdminToEmployeeNotification($request->message));
        }

        // broadcast event
        event(new SendNotificationEvent($request->message));

        return response()->json(['success' => true]);
    }
}