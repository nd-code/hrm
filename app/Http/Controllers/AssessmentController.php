<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Employee;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index()
    {
        $assessments = Assessment::with(['employee', 'reviewer'])->latest()->paginate(10);
        return view('assessments.index', compact('assessments'));
    }

    public function create()
    {
        $employees = Employee::orderBy('id', 'desc')->get();
        return view('assessments.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'assessment_date' => 'required|date',
			'type' => 'required|in:Weekly,Quarterly,Half Yearly,Yearly',
			'final_conclusion' => 'nullable|string|max:5000',
        ] + collect(range(1,15))->mapWithKeys(fn($i)=>["comment_$i"=>'nullable|string'])->toArray());

        $data['reviewer_id'] = auth()->id();

        Assessment::create($data);

        return redirect()->route('assessments.index')->with('success','Assessment created successfully.');
    }

    public function show(Assessment $assessment)
    {
        $assessment->load(['employee','reviewer']);
        return view('assessments.show', compact('assessment'));
    }

    public function edit(Assessment $assessment)
    {
        $employees = Employee::orderBy('id', 'desc')->get();
        return view('assessments.edit', compact('assessment','employees'));
    }

    public function update(Request $request, Assessment $assessment)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'assessment_date' => 'required|date',
			'type' => 'required|in:Weekly,Quarterly,Half Yearly,Yearly',
			'final_conclusion' => 'nullable|string|max:5000',
        ] + collect(range(1,15))->mapWithKeys(fn($i)=>["comment_$i"=>'nullable|string'])->toArray());

        $data['reviewer_id'] = auth()->id();

        $assessment->update($data);

        return redirect()->route('assessments.index')->with('success','Assessment updated successfully.');
    }

    public function destroy(Assessment $assessment)
    {
        $assessment->delete();
        return redirect()->route('assessments.index')->with('success','Assessment deleted.');
    }
}