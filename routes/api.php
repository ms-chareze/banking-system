<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// The "web" group is added so the logged-in session cookie authenticates the AJAX polling request.
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])
        ->name('api.dashboard.stats');
});