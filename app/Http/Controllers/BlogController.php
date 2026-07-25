<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogRequest;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Blog::latest()->paginate($request->input('per_page', 10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BlogRequest $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $path = $request->file('image')->store('blogs', 'public');

        $payload = [
            ...$request->validated(),
            'image' => $path
        ];

        return Blog::create($payload);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        return Blog::where('slug', $slug)->firstOrFail();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BlogRequest $request, Blog $blog)
    {   
        if ($request->hasFile('image')) {
            if ($blog->image && Storage::disk('public')->exists($blog->image)) {
                Storage::disk('public')->delete($blog->image);
            }
            $path = $request->file('image')->store('blogs', 'public');
        }
        $payload = [
            ...$request->validated(),
            'image' => $path ?? $blog->image
        ];
        $blog->update($payload);
        return Blog::find($blog->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        $blog->delete();
        return response()->json([
            'message' => 'Blog archived successfully'
        ], 200);
    }

    public function archiveList(Request $request) {
        return Blog::onlyTrashed()->paginate($request->input('per_page', 10));
    }

    public function archiveRestore($id) {
        $blog = Blog::onlyTrashed()->findOrFail($id);
        $blog->restore();
        return response()->json([
            'message' => 'Blog restored successfully'
        ], 200);
    }

    public function archiveDelete($id) {
        $blog = Blog::onlyTrashed()->findOrFail($id);
        if ($blog->image && Storage::disk('public')->exists($blog->image)) {
            Storage::disk('public')->delete($blog->image);
        }
        $blog->forceDelete();
        return response()->json([
            'message' => 'Blog deleted successfully'
        ], 200);
    }
}
