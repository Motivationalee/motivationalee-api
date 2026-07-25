<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsultationRequest;
use App\Mail\ConsultationBookedMail;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

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
        $consultation = Consultation::create($request->validated());

        try {
            Mail::to(config('mail.from.address'))->send(new ConsultationBookedMail($consultation));
        } catch (Throwable $e) {
            Log::error('Failed to send consultation booked email.', [
                'consultation_id' => $consultation->id,
                'to' => config('mail.from.address'),
                'error' => $e->getMessage(),
            ]);
        }

        return $consultation;
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
    public function update(Request $request, Consultation $consultation)
    {
        $validated = $request->validate([
            'is_replied' => 'required|boolean',
        ]);

        $consultation->update($validated);

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
