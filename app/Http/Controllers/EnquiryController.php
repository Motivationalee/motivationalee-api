<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\Request;
use App\Http\Requests\EnquiryRequest;

class EnquiryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Enquiry::paginate($request->input('page_size', 10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EnquiryRequest $request)
    {
        /** apply email logics here */
        return Enquiry::create($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(Enquiry $enquiry)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Enquiry $enquiry)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:pending,approved,rejected']
        ]);
        $enquiry->update($request->only('status'));
        return Enquiry::find($enquiry->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();
        return response()->json([
            'message' => 'Enquiry archived successfully'
        ], 200);
    }

    public function archiveList(Request $request) {
        return Enquiry::onlyTrashed()->paginate($request->input('per_page', 10));
    }

    public function archiveRestore($id) {
        $enquiry = Enquiry::onlyTrashed()->findOrFail($id);
        $enquiry->restore();
        return response()->json([
            'message' => 'Enquiry restored successfully'
        ], 200);
    }

    public function archiveDelete($id) {
        $enquiry = Enquiry::onlyTrashed()->findOrFail($id);
        $enquiry->forceDelete();
        return response()->json([
            'message' => 'Enquiry deleted successfully'
        ], 200);
    }
}
