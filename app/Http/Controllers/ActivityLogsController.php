<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\EmployeeDocument;
use Illuminate\Support\Facades\Storage;
use App\Models\WorkSession;
use App\Models\Leave;
use Carbon\Carbon;
use App\Models\Position;
use App\Models\ActivityLog;

class ActivityLogsController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('user')->latest()->get();

        return view('activity.index', compact('logs'));
    }
}
