<?php

use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

// API Routes for Reports (returns JSON for charts)
Route::middleware(['auth'])->group(function () {
    // Monthly report data for bar charts
    Route::get('/reports/month', [ReportController::class, 'month'])->name('api.reports.month');

    // Yearly report data for bar charts
    Route::get('/reports/year', [ReportController::class, 'year'])->name('api.reports.year');
});
