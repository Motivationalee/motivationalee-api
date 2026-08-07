<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Consultation;
use App\Models\Gallery;
use App\Models\Testimonial;
use App\Models\YoutubeContent;
use App\Models\Enquiry;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard($type) {
        $data = null;
        switch($type) {
            case 'count':
                $data = $this->counts();
            break;
            default:
                return response()->json([
                    'message' => 'Not found.'
                ], 404);
        }

        return response()->json([
            'message' => 'Data retrieved successfully',
            'data' => $data
        ], 200);
    }

    private function counts() {
        $blogs = Blog::count();
        $testimonials = Testimonial::count();
        $galleries = Gallery::count();
        $youtubeContents = YoutubeContent::count();
        $consultations = Consultation::count();
        $enquiries = Enquiry::count();

        return [
            'blogs' => $blogs,
            'testimonials' => $testimonials,
            'galleries' => $galleries,
            'youtubeContents' => $youtubeContents,
            'consultations' => $consultations,
            'enquiries' => $enquiries
        ];
    }
}
