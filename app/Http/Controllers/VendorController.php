<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Services\RecycleBinService;

class VendorController extends Controller
{
    public function index()
    {
        return view('vendors.index', [
            'vendors' => Vendor::orderBy('created_at', 'desc')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'category'       => 'required|string|max:100',
            'date'           => 'nullable|date',
            'remark'         => 'nullable|string',
        ]);

        $vendor = Vendor::create([
            'name'           => $request->name,
            'contact_number' => $request->contact_number,
            'category'       => $request->category,
            'date'           => $request->date,
            'remark'         => $request->remark,
        ]);

        // If it's an AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'id'             => $vendor->id,
                'name'           => $vendor->name,
                'contact_number' => $vendor->contact_number,
                'category'       => $vendor->category,
                'date'           => $vendor->date,
                'remark'         => $vendor->remark,
            ]);
        }

        // Fallback for normal requests
        return back()->with('success', 'Vendor added successfully.');
    }

    public function show(Vendor $vendor)
    {
        return view('vendors.show', compact('vendor'));
    }

    public function destroy(Vendor $vendor)
    {
        RecycleBinService::delete($vendor, 'vendor');

        $vendor->delete();
        return back()->with('success', 'Vendor deleted successfully.');
    }

    public function inlineUpdate(Request $request, Vendor $vendor)
    {
        // Detect which field was updated (only update one field like EmployeeController)
        $field = array_keys($request->except('_token'))[0];
        $vendor->update([$field => $request->$field]);

        return response()->json(['success' => true]);
    }
}