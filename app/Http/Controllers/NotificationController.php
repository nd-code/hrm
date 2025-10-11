<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Notifications\AdminToEmployeeNotification;
use App\Events\SendNotificationEvent;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = DB::table('notifications as n1')
            ->join(DB::raw('(SELECT notifiable_id, MAX(created_at) as latest_created_at 
                             FROM notifications 
                             GROUP BY notifiable_id) as n2'),
                function($join) {
                    $join->on('n1.notifiable_id', '=', 'n2.notifiable_id')
                         ->on('n1.created_at', '=', 'n2.latest_created_at');
                })
            ->select('n1.*')
            ->orderBy('n1.created_at', 'desc')
            ->get();

        return view('notifications', compact('notifications'));
    }
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