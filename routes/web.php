<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupportPlanController;

// Redirect root to support plans index
Route::get('/', function () {
    return redirect()->route('support-plans.index');
});

// Support Plan resource routes - Add auth middleware to all support plan routes
Route::middleware(['auth'])->group(function () {
Route::resource('support-plans', SupportPlanController::class);

// Export routes
Route::get('support-plans/{id}/export-excel', [SupportPlanController::class, 'exportToExcel'])
    ->name('support-plans.exportToExcel');

Route::get('support-plans/{id}/export-word', [SupportPlanController::class, 'exportToWord'])
    ->name('support-plans.exportToWord');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
