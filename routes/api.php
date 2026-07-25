<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\YoutubeContentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);
Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blog/{slug}', [BlogController::class, 'show']);
Route::get('/testimonials', [TestimonialController::class, 'index']);
Route::get('/youtube-contents', [YoutubeContentController::class, 'index']);
Route::get('/galleries', [GalleryController::class, 'index']);
Route::post('/book-consultation', [ConsultationController::class, 'store']);

Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('admin')->group(function() {
        Route::get('dashboard/{type}', [DashboardController::class, 'dashboard']);

        Route::apiResource('services', ServiceController::class)->except(['show']);
        Route::apiResource('blogs', BlogController::class)->except(['show']);
        Route::apiResource('testimonials', TestimonialController::class)->except(['show']);
        Route::apiResource('galleries', GalleryController::class)->except(['show']);
        Route::apiResource('youtube-contents', YoutubeContentController::class)->except(['show']);
        Route::apiResource('consultations', ConsultationController::class)->except(['show']);
        Route::apiResource('users', UserController::class)->except(['show']);

        Route::prefix('archived')->group(function() {
            Route::get('services', [ServiceController::class, 'archiveList']);
            Route::get('services/{id}', [ServiceController::class, 'archiveRestore']);
            Route::delete('services/{id}', [ServiceController::class, 'archiveDelete']);

            Route::get('blogs', [BlogController::class, 'archiveList']);
            Route::get('blogs/{id}', [BlogController::class, 'archiveRestore']);
            Route::delete('blogs/{id}', [BlogController::class, 'archiveDelete']);

            Route::get('testimonials', [TestimonialController::class, 'archiveList']);
            Route::get('testimonials/{id}', [TestimonialController::class, 'archiveRestore']);
            Route::delete('testimonials/{id}', [TestimonialController::class, 'archiveDelete']);

            Route::get('galleries', [GalleryController::class, 'archiveList']);
            Route::get('galleries/{id}', [GalleryController::class, 'archiveRestore']);
            Route::delete('galleries/{id}', [GalleryController::class, 'archiveDelete']);

            Route::get('youtube-contents', [YoutubeContentController::class, 'archiveList']);
            Route::get('youtube-contents/{id}', [YoutubeContentController::class, 'archiveRestore']);
            Route::delete('youtube-contents/{id}', [YoutubeContentController::class, 'archiveDelete']);

            Route::get('consultations', [ConsultationController::class, 'archiveList']);
            Route::get('consultations/{id}', [ConsultationController::class, 'archiveRestore']);
            Route::delete('consultations/{id}', [ConsultationController::class, 'archiveDelete']);

            Route::get('users', [UserController::class, 'archiveList']);
            Route::get('users/{id}', [UserController::class, 'archiveRestore']);
            Route::delete('users/{id}', [UserController::class, 'archiveDelete']);
        });
    });
});
