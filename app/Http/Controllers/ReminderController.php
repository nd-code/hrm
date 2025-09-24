<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReminderController extends Controller
{
    public function index()
    {
        $reminders = Reminder::where('employee_id', auth('employee')->id())
            ->latest()
            ->get();

        return view('employee.reminders.index', compact('reminders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'        => 'required|date',
            'subject'     => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $reminder = Reminder::create([
            'employee_id' => auth('employee')->id(),
            'date'        => $request->date,
            'subject'     => $request->subject,
            'description' => $request->description,
            'status'      => 'Pending', // always default
        ]);

        if ($request->ajax()) {
            return response()->json($reminder);
        }

        return back()->with('success', 'Reminder created successfully.');
    }

    public function show(Reminder $reminder)
    {
        return view('employee.reminders.show', compact('reminder'));
    }

    public function destroy(Reminder $reminder)
    {
        $reminder->delete();
        return back()->with('success', 'Reminder deleted successfully.');
    }

    // ✅ Mark as Completed
    public function complete(Reminder $reminder)
    {
        $reminder->update(['status' => 'Completed']);
        return back()->with('success', 'Reminder marked as completed.');
    }

    // ✅ Dashboard view: only due/overdue Pending reminders
    public function dashboard()
    {
        $today = Carbon::today()->toDateString();

        $reminders = Reminder::where('status', 'Pending')
            ->whereDate('date', '<=', $today) // due today or overdue
            ->orderBy('date', 'asc')
            ->get();

        return view('employee.reminders.dashboard', compact('reminders'));
    }

    public function inlineUpdate(Request $request, Reminder $reminder)
    {
        $field = array_keys($request->except('_token'))[0];
        $reminder->update([$field => $request->$field]);

        return response()->json(['success' => true]);
    }
}