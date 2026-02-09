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
use App\Services\RecycleBinService;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('employees.index', [
            'employees' => Employee::orderBy('created_at', 'desc')->get(),
            'positions' => Position::orderBy('id')->get(), // ✅ fetch positions
        ]);
    }

    public function store(Request $request)
	{
		$request->validate([
			'name' => 'required',
			'email' => 'required|email|unique:employees,email',
			'employee_id' => 'nullable|string|unique:employees,employee_id',
			'joining_date' => 'nullable|date'
		]);

		$password = Str::random(8);

		$employee = Employee::create([
			'name' => $request->name,
			'email' => $request->email,
			'employee_id' => $request->employee_id,
			'phone' => $request->phone,
			'position' => $request->position,
			'pan_number' => $request->pan_number,
			'joining_date' => $request->joining_date,
			'address' => $request->address,
			'password' => bcrypt($password),
		]);

		// Send login details to employee email
		Mail::raw("Hi {$employee->name},\n\nYour account has been created.\nEmail: {$employee->email}\nPassword: {$password}\n\nYou can login here:\n" . url('/employee/login'), function ($msg) use ($employee) {
			$msg->to($employee->email)->subject('Your Employee Account');
		});

		// If it's an AJAX request, return JSON
		if ($request->ajax()) {
			return response()->json([
				'id' => $employee->id,
				'name' => $employee->name,
				'email' => $employee->email,
				'employee_id' => $employee->employee_id,
				'phone' => $employee->phone,
				'position' => $employee->position,
				'pan_number' => $employee->pan_number,
				'joining_date' => $employee->joining_date
			]);
		}

		// Fallback for normal requests
		return back()->with('success', 'Employee added and email sent.');
	}

    public function show(Request $request, Employee $employee)
    {
        $sessions = WorkSession::where('employee_id', $employee->id)
            ->orderBy('id', 'desc')
            ->get();

        // Selected year (default = current year)
        $year = $request->year ?? now()->year;

        // All leaves (for table)
        $leaves = Leave::where('employee_id', $employee->id)
            ->whereYear('from_date', $year)
            ->orderBy('id', 'desc')
            ->get();

        // Approved leaves
        $approvedLeaves = $leaves->filter(fn ($leave) =>
            strtolower($leave->status) === 'approved'
        );

        // LWP leaves
        $lwpLeaves = $leaves->filter(fn ($leave) =>
            strtolower($leave->status) === 'lwp'
        );

        // Total approved leaves
        $totalLeaves = 0;
        $totalLwp = 0;

        foreach ($approvedLeaves as $leave) {
            $days = $this->calculateLeaveDays($leave);
            $leave->days = $days;
            $totalLeaves += $days;
        }

        foreach ($lwpLeaves as $leave) {
            $days = $this->calculateLeaveDays($leave);
            $leave->days = $days;
            $totalLwp += $days;
        }

        // Month-wise approved leaves (year-safe)
        $monthWiseApproved = $approvedLeaves
            ->groupBy(fn ($leave) =>
                Carbon::parse($leave->from_date)->format('Y-m')
            )
            ->map(fn ($group) => $group->sum('days'));
        
        // Month-wise LWP leaves (year-safe)
        $monthWiseLwp = $lwpLeaves
            ->groupBy(fn ($leave) =>
                Carbon::parse($leave->from_date)->format('Y-m')
            )
            ->map(fn ($group) => $group->sum('days'));

        // Year dropdown options (last 5 years)
        $years = range(now()->year, now()->year - 5);

        $positions = Position::orderBy('id')->get();

        return view('employees.show', compact(
            'employee',
            'sessions',
            'leaves',
            'totalLeaves',
            'totalLwp',
            'monthWiseApproved',
            'monthWiseLwp',
            'positions',
            'year',
            'years'
        ));
    }

    public function destroy(Employee $employee)
    {
        RecycleBinService::delete($employee, 'employee');
        
        $employee->delete();
        return back()->with('success', 'Deleted.');
    }

    public function inlineUpdate(Request $request, Employee $employee)
    {
        $field = array_keys($request->except('_token'))[0];
        $employee->update([$field => $request->$field]);

        return response()->json(['success' => true]);
    }
	
	public function uploadDocuments(Request $request, $id)
	{
		$request->validate([
			'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // 5MB max
		]);

		$uploadedFiles = [];
		foreach ($request->file('documents', []) as $file) {
			$path = $file->store('employee_documents', 'public');
			$document = EmployeeDocument::create([
				'employee_id' => $id,
				'file_name' => $file->getClientOriginalName(),
				'file_path' => $path,
			]);
			$uploadedFiles[] = $document;
		}

		return response()->json(['success' => true, 'documents' => $uploadedFiles]);
	}

	public function getDocuments($id)
	{
		$documents = EmployeeDocument::where('employee_id', $id)->get();
		return response()->json(['success' => true, 'documents' => $documents]);
	}
	
	public function deleteDocument($employeeId, $documentId)
	{
		$document = EmployeeDocument::where('employee_id', $employeeId)->findOrFail($documentId);

		// Delete file from storage
		if (Storage::disk('public')->exists($document->file_path)) {
			Storage::disk('public')->delete($document->file_path);
		}

		// Delete record
		$document->delete();

		return response()->json(['success' => true]);
	}
	
	public function relievingLetter($id)
    {
        $employee = Employee::findOrFail($id);

        return view('employees.relieving', compact('employee'));
    }
	
	public function profile()
	{
		$employee = auth()->user(); // logged-in employee

		return view('employee.profile', compact('employee'));
	}
        
        public function details(Request $request, $id)
        {
            $employee = Employee::findOrFail($id);

            $sessions = WorkSession::where('employee_id', $id)
                ->orderBy('id', 'desc')
                ->get();

            // Selected year (default = current year)
            $year = $request->year ?? now()->year;

            // All leaves (for table)
            $leaves = Leave::where('employee_id', $id)
                ->whereYear('from_date', $year)
                ->orderBy('id', 'desc')
                ->get();

            // Approved leaves
            $approvedLeaves = $leaves->filter(fn ($leave) =>
                strtolower($leave->status) === 'approved'
            );

            // LWP leaves
            $lwpLeaves = $leaves->filter(fn ($leave) =>
                strtolower($leave->status) === 'lwp'
            );

            // Total approved leaves
            $totalLeaves = 0;
            $totalLwp = 0;

            foreach ($approvedLeaves as $leave) {
                $days = $this->calculateLeaveDays($leave);
                $leave->days = $days;
                $totalLeaves += $days;
            }

            foreach ($lwpLeaves as $leave) {
                $days = $this->calculateLeaveDays($leave);
                $leave->days = $days;
                $totalLwp += $days;
            }

            // Month-wise approved leaves
            $monthWiseApproved = $approvedLeaves
                ->groupBy(fn ($leave) =>
                    Carbon::parse($leave->from_date)->format('Y-m')
                )
                ->map(fn ($group) => $group->sum('days'));
            
            // Month-wise LWP leaves
            $monthWiseLwp = $lwpLeaves
                ->groupBy(fn ($leave) =>
                    Carbon::parse($leave->from_date)->format('Y-m')
                )
                ->map(fn ($group) => $group->sum('days'));

            // Years for dropdown (last 5 years)
            $years = range(now()->year, now()->year - 5);

            $positions = Position::orderBy('id')->get();

            return view('employees.show', compact(
                'employee',
                'sessions',
                'leaves',
                'totalLeaves',
                'totalLwp',
                'monthWiseApproved',
                'monthWiseLwp',
                'positions',
                'year',
                'years'
            ));
        }
        
        public function attendanceFilter(Request $request, $id)
        {
            $query = WorkSession::where('employee_id', $id);

            if ($request->from_date) {
                $query->whereDate('work_date', '>=', $request->from_date);
            }

            if ($request->to_date) {
                $query->whereDate('work_date', '<=', $request->to_date);
            }

            // DataTables parameters
            $draw   = $request->get('draw');
            $start  = $request->get('start');
            $length = $request->get('length');

            $recordsTotal = $query->count();

            // Fetch paginated data
            $rows = $query
                ->orderBy('id', 'desc')
                ->skip($start)
                ->take($length)
                ->get();

            // Format data manually
            $data = [];

            foreach ($rows as $row) {

                // Format dates
                $workDate = $row->work_date
                    ? \Carbon\Carbon::parse($row->work_date)->format('d-m-Y')
                    : '-';

                $online = $row->start_time
                    ? \Carbon\Carbon::parse($row->start_time)->format('h:i A')
                    : '-';

                $offline = $row->end_time
                    ? \Carbon\Carbon::parse($row->end_time)->format('h:i A')
                    : '-';

                // Calculate total hours
                if ($row->start_time && $row->end_time) {
                    $start = \Carbon\Carbon::parse($row->start_time);
                    $end   = \Carbon\Carbon::parse($row->end_time);
                    $total = gmdate('H:i:s', $end->diffInSeconds($start));
                } else {
                    $total = '-';
                }

                $data[] = [
                    'id'           => $row->id,
                    'work_date'    => $workDate,
                    'online'       => $online,
                    'offline'      => $offline,
                    'total'        => $total,
                    'project_name' => $row->project_name,
                    'comment'      => $row->comment,
                ];
            }

            // Return DataTables JSON format
            return response()->json([
                'draw'            => intval($draw),
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsTotal,
                'data'            => $data
            ]);
        }
        
        public function attendanceExportPdf(Request $request, $id)
        {
            $query = WorkSession::where('employee_id', $id);

            if ($request->from_date)
                $query->whereDate('work_date', '>=', $request->from_date);

            if ($request->to_date)
                $query->whereDate('work_date', '<=', $request->to_date);

            $data = $query->orderBy('id', 'desc')->get();

            $employee = Employee::find($id);

            $pdf = \PDF::loadView('pdf.attendances', compact('data', 'employee'))
                        ->setPaper('a4', 'portrait');

            return $pdf->download('attendance-report-' . now()->format('Y-m-d') . '.pdf');
        }
        
        private function calculateLeaveDays($leave)
        {
            if (strtolower($leave->leave_type) === 'half day leave') {
                return 0.5;
            }

            return \Carbon\Carbon::parse($leave->from_date)
                ->diffInDays(\Carbon\Carbon::parse($leave->to_date)) + 1;
        }
        
        public function salarySlip($id)
	{
            /*$employee = Employee::findOrFail($id);

            $position = Position::where('id', $employee->position)->first();

            $letter = \App\Models\AppointmentLetter::where('employee_id', $employee->id)->first();

            return view('employees.appointment', compact('employee', 'position', 'letter'));*/
            
            return view('employees.salaryslip');
	}
}
