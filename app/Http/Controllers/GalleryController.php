<?php

namespace App\Http\Controllers;

use App\Http\Requests\GalleryRequest;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Gallery::paginate($request->input('per_page', 10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GalleryRequest $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $path = $request->file('image')->store('galleries', 'public');

        $payload = [
            ...$request->validated(),
            'image' => $path
        ];

        return Gallery::create($payload);
    }

    /**
     * Display the specified resource.
     */
    public function show(Gallery $gallery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GalleryRequest $request, Gallery $gallery)
    {
        if ($request->hasFile('image')) {
            if ($gallery->image && Storage::disk('public')->exists($gallery->image)) {
                Storage::disk('public')->delete($gallery->image);
            }
            $path = $request->file('image')->store('galleries', 'public');
        }
        $payload = [
            ...$request->validated(),
            'image' => $path ?? $gallery->image
        ];
        $gallery->update($payload);
        return Gallery::find($gallery->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gallery $gallery)
    {
        $gallery->delete();
        return response()->json([
            'message' => 'Gallery archived successfully'
        ], 200);
    }

    public function archiveList(Request $request) {
        return Gallery::onlyTrashed()->paginate($request->input('per_page', 10));
    }

    public function archiveRestore($id) {
        $gallery = Gallery::onlyTrashed()->findOrFail($id);
        $gallery->restore();
        return response()->json([
            'message' => 'Gallery restored successfully'
        ], 200);
    }

    public function archiveDelete($id) {
        $gallery = Gallery::onlyTrashed()->findOrFail($id);
        if ($gallery->image && Storage::disk('public')->exists($gallery->image)) {
            Storage::disk('public')->delete($gallery->image);
        }
        $gallery->forceDelete();
        return response()->json([
            'message' => 'Gallery deleted successfully'
        ], 200);
    }
}
