<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\RecycleBinService;

class CandidateController extends Controller
{
    public function index()
    {
        $candidates = Candidate::latest()->get();
        return view('candidates.index', compact('candidates'));
    }

    public function create()
    {
        return view('candidates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'city' => 'nullable',
            'salary' => 'nullable',
            'work_experience' => 'nullable',
            'designation' => 'nullable',
            'interview_date' => 'nullable|date',
            'comment' => 'nullable|string',
            'cv' => 'nullable|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($request->hasFile('cv')) {
            $data['cv'] = $request->file('cv')->store('cvs', 'public');
        }

        Candidate::create($data);

        return redirect()
            ->route('candidates.index')
            ->with('success', 'Candidate added successfully!');
    }

    public function edit(Candidate $candidate)
    {
        return view('candidates.edit', compact('candidate'));
    }

    public function update(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'city' => 'nullable',
            'salary' => 'nullable',
            'work_experience' => 'nullable',
            'designation' => 'nullable',
            'interview_date' => 'nullable|date',
            'comment' => 'nullable|string',
            'cv' => 'nullable|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($request->hasFile('cv')) {
            $data['cv'] = $request->file('cv')->store('cvs', 'public');
        }

        $candidate->update($data);

        return redirect()
            ->route('candidates.index')
            ->with('success', 'Candidate updated successfully!');
    }

    public function destroy(Candidate $candidate)
    {
        RecycleBinService::delete($candidate, 'candidate');
        
        if ($candidate->cv && file_exists(storage_path("app/public/{$candidate->cv}"))) {
            unlink(storage_path("app/public/{$candidate->cv}"));
        }

        $candidate->delete();

        return back()->with('success', 'Candidate deleted successfully');
    }
    
    public function inlineUpdate(Request $request)
    {
        $candidate = Candidate::findOrFail($request->id);

        if ($request->field == 'interview_date') {
            $candidate->interview_date = Carbon::parse($request->value);
        } else {
            $candidate->{$request->field} = $request->value;
        }
        $candidate->save();

        return response()->json(['success' => true]);
    }
}