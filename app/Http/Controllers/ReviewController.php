<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Employee;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with('employee')->orderBy('id', 'desc')->get();
        $employees = Employee::orderBy('id', 'desc')->get();
        return view('reviews.index', compact('reviews', 'employees'));
    }

    public function store(Request $request)
	{
		$request->validate([
			'employee_id' => 'required|exists:employees,id',
			'project_name' => 'required',
			'date_from' => 'required|date',
			'date_to' => 'required|date|after_or_equal:date_from',
			'review' => 'required',
		]);

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
        $review->delete();
        return back()->with('success', 'Review deleted.');
    }
	
	public function update(Request $request, $id)
	{
		$review = Review::findOrFail($id);
		$review->update($request->only(['project_name', 'date_from', 'date_to', 'review']));
		return response()->json(['success' => true]);
	}
}
