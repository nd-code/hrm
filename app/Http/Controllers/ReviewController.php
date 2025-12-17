<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function index()
    {
        if (auth()->id() == 101) {
            $reviews = Review::with('employee')->orderBy('id', 'desc')->get();
            $employees = Employee::orderBy('id', 'desc')->get();
            return view('reviews.index', compact('reviews', 'employees'));
        } elseif (auth('employee')->check()) {
            $name = Auth::guard('employee')->user()->name;
            $reviews = Review::with('employee')->where('review_given_by', $name)->orderBy('id', 'desc')->get();
            $teamEmployees = Team::with('employee')
                ->where('parent_employee_id', auth('employee')->id())
                ->get();

            $existingIds = $teamEmployees->pluck('employee_id')->toArray();

            $employees = Employee::whereIn('id', $existingIds)
                ->where('id', '!=', auth('employee')->id())
                ->get();
            return view('reviews.indexlist', compact('reviews', 'employees'));
        }
    }

    public function store(Request $request)
	{
		$request->validate([
			'employee_id' => 'required|exists:employees,id',
			'project_name' => 'required',
			'date_from' => 'required|date',
			'date_to' => 'required|date|after_or_equal:date_from',
			'review_given_by' => 'required|string|max:255',
			'review' => 'required',
		]);
                
                if (Auth::guard('web')->check() && Auth::id() == 101) {

                    $request->merge([
                        'review_given_by' => 'Super Admin'
                    ]);

                } elseif (Auth::guard('employee')->check()) {

                    $request->merge([
                        'review_given_by' => Auth::guard('employee')->user()->name
                    ]);
                }
                
                //echo '<pre>';
                //print_r($_POST);
                //exit;

		$review = Review::create($request->all());

		// Load the employee relationship so AJAX can access it
		$review->load('employee');

		return response()->json($review);
	}

    public function show($id)
	{
		$review = Review::with('employee')->findOrFail($id);
		return view('reviews.show', compact('review'));
	}

    public function destroy(Review $review)
    {
        if (auth()->id() == 101) {
            $review->delete();
        }else{
            $id = request()->segment(count(request()->segments()));
            Review::where('id', $id)->delete();
        }
        return back()->with('success', 'Review deleted.');
    }
	
	public function update(Request $request, $id)
	{
		$review = Review::findOrFail($id);
		$review->update($request->only(['project_name', 'date_from', 'date_to', 'review_given_by', 'review']));
		return response()->json(['success' => true]);
	}
	
	public function inlineUpdate(Request $request, $id)
	{
		$review = \App\Models\Review::findOrFail($id);

		$review->update($request->only([
			'project_name',
			'date_from',
			'date_to',
			'review_given_by',
			'review'
		]));

		return response()->json(['success' => true]);
	}
}
