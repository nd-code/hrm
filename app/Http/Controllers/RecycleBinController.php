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
use App\Models\RecycleBin;
use App\Services\RecycleBinService;

class RecycleBinController extends Controller
{
    public function index()
    {
        $items = RecycleBin::latest()->paginate(20);

        return view('recycle.index', compact('items'));
    }

    public function restore($id)
    {
        RecycleBinService::restore($id);

        return back()->with('success', 'Record restored');
    }

    public function destroy($id)
    {
        RecycleBinService::forceDelete($id);

        return back()->with('success', 'Record permanently deleted');
    }
}
