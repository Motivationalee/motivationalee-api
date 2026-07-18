<?php

namespace App\Http\Controllers;

use App\Http\Requests\TestimonialRequest;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Testimonial::paginate($request->input('per_page', 10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TestimonialRequest $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $path = $request->file('image')->store('testimonials', 'public');

        $payload = [
            ...$request->validated(),
            'image' => $path
        ];

        return Testimonial::create($payload);
    }

    /**
     * Display the specified resource.
     */
    public function show(Testimonial $testimonial)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TestimonialRequest $request, Testimonial $testimonial)
    {
        if ($request->hasFile('image')) {
            if ($testimonial->image && Storage::disk('public')->exists($testimonial->image)) {
                Storage::disk('public')->delete($testimonial->image);
            }
            $path = $request->file('image')->store('testimonials', 'public');
        }
        $payload = [
            ...$request->validated(),
            'image' => $path ?? $testimonial->image
        ];
        $testimonial->update($payload);
        return Testimonial::find($testimonial->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return response()->json([
            'message' => 'Testimonial archived successfully'
        ], 200);
    }

    public function archiveList(Request $request) {
        return Testimonial::onlyTrashed()->paginate($request->input('per_page', 10));
    }

    public function archiveRestore($id) {
        $testimonial = Testimonial::onlyTrashed()->findOrFail($id);
        $testimonial->restore();
        return response()->json([
            'message' => 'Testimonial restored successfully'
        ], 200);
    }

    public function archiveDelete($id) {
        $testimonial = Testimonial::onlyTrashed()->findOrFail($id);
        if ($testimonial->image && Storage::disk('public')->exists($testimonial->image)) {
            Storage::disk('public')->delete($testimonial->image);
        }
        $testimonial->forceDelete();
        return response()->json([
            'message' => 'Testimonial deleted successfully'
        ], 200);
    }
}
