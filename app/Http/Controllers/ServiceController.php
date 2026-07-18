<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Service::paginate($request->input('per_page', 10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceRequest $request)
    {
        return Service::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceRequest $request, Service $service)
    {
        $service->update($request->all());
        return Service::find($service->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();
        return response()->json([
            'message' => 'Service deleted successfully'
        ], 200);
    }

    public function archiveList(Request $request) {
        return Service::onlyTrashed()->paginate($request->input('per_page', 10));
    }

    public function archiveRestore($id) {
        $service = Service::onlyTrashed()->findOrFail($id);
        $service->restore();
        return response()->json([
            'message' => 'Service restored successfully'
        ], 200);
    }

    public function archiveDelete($id) {
        $service = Service::onlyTrashed()->findOrFail($id);
        $service->forceDelete();
        return response()->json([
            'message' => 'Service deleted successfully'
        ], 200);
    }
}
