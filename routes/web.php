<?php

use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    return redirect()->away(config('app.web_app_url'));
});