<?php

namespace App\Http\Controllers;

use App\Http\Requests\YoutubeContentRequest;
use App\Models\YoutubeContent;
use Illuminate\Http\Request;

class YoutubeContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return YoutubeContent::paginate($request->input('per_page', 10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(YoutubeContentRequest $request)
    {
        return YoutubeContent::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(YoutubeContent $youtubeContent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(YoutubeContentRequest $request, YoutubeContent $youtubeContent)
    {
        $youtubeContent->update($request->all());
        return YoutubeContent::find($youtubeContent->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(YoutubeContent $youtubeContent)
    {
        $youtubeContent->delete();
        return response()->json([
            'message' => 'Youtube Content archived successfully'
        ], 200);
    }

    public function archiveList(Request $request) {
        return YoutubeContent::onlyTrashed()->paginate($request->input('per_page', 10));
    }

    public function archiveRestore($id) {
        $youtubeContent = YoutubeContent::onlyTrashed()->findOrFail($id);
        $youtubeContent->restore();
        return response()->json([
            'message' => 'Youtube Content restored successfully'
        ], 200);
    }

    public function archiveDelete($id) {
        $youtubeContent = YoutubeContent::onlyTrashed()->findOrFail($id);
        $youtubeContent->forceDelete();
        return response()->json([
            'message' => 'Youtube Content deleted successfully'
        ], 200);
    }
}
