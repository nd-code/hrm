<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Holiday;
use App\Services\RecycleBinService;

class HolidayController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ADMIN SIDE
    |--------------------------------------------------------------------------
    */

    public function adminIndex()
    {
        $holidays = Holiday::orderBy('holiday_date', 'DESC')
            ->paginate(10);

        return view('holidays.index', compact('holidays'));
    }

    public function create()
    {
        return view('holidays.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'holiday_date' => 'required|date',
            'day' => 'required|string'
        ]);

        Holiday::create([
            'title'         => $request->title,
            'holiday_date'  => $request->holiday_date,
            'description'   => $request->description,
            'day'           => $request->day,
            'status'        => 1,
        ]);

        return redirect()
            ->route('holidays.index')
            ->with('success', 'Holiday added successfully.');
    }

    public function edit($id)
    {
        $holiday = Holiday::findOrFail($id);

        return view('holidays.edit', compact('holiday'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'holiday_date' => 'required|date',
            'day' => 'required|string'
        ]);

        $holiday = Holiday::findOrFail($id);

        $holiday->update([
            'title'         => $request->title,
            'holiday_date'  => $request->holiday_date,
            'description'   => $request->description,
            'day'           => $request->day,
        ]);

        return redirect()
            ->route('holidays.index')
            ->with('success', 'Holiday updated successfully.');
    }

    public function destroy($id)
    {
        $holiday = Holiday::findOrFail($id);
        
        RecycleBinService::delete($holiday, 'holiday');

        $holiday->delete();

        return redirect()
            ->route('holidays.index')
            ->with('success', 'Holiday deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | EMPLOYEE SIDE
    |--------------------------------------------------------------------------
    */

    public function employeeHolidayIndex()
    {
        $holidays = Holiday::where('status', 1)
            ->orderBy('holiday_date', 'ASC')
            ->get();

        return view('employee.holidays.index', compact('holidays'));
    }
}