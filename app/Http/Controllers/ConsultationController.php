<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsultationRequest;
use App\Models\Consultation;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Consultation::latest()->orderBy('is_replied')->paginate($request->input('per_page', 10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ConsultationRequest $request)
    {
        return Consultation::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(Consultation $consultation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ConsultationRequest $request, Consultation $consultation)
    {
        $consultation->update($request->all());
        return Consultation::find($consultation->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Consultation $consultation)
    {
        $consultation->delete();
        return response()->json([
            'message' => 'Consultation archived successfully'
        ], 200);
    }

    public function archiveList(Request $request) {
        return Consultation::onlyTrashed()->paginate($request->input('per_page', 10));
    }

    public function archiveRestore($id) {
        $consultation = Consultation::onlyTrashed()->findOrFail($id);
        $consultation->restore();
        return response()->json([
            'message' => 'Consultationt restored successfully'
        ], 200);
    }

    public function archiveDelete($id) {
        $consultation = Consultation::onlyTrashed()->findOrFail($id);
        $consultation->forceDelete();
        return response()->json([
            'message' => 'Consultation deleted successfully'
        ], 200);
    }
}
