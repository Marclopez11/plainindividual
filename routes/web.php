<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupportPlanController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\ExcelWordController;

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

    // Timetable routes
    Route::get('support-plans/{supportPlan}/timetables', [TimetableController::class, 'index'])
        ->name('timetables.index');
    Route::post('support-plans/{supportPlan}/timetables', [TimetableController::class, 'store'])
        ->name('timetables.store');
    Route::get('timetables/{timetable}', [TimetableController::class, 'show'])
        ->name('timetables.show');
    Route::put('timetables/{timetable}', [TimetableController::class, 'update'])
        ->name('timetables.update');
    Route::delete('timetables/{timetable}', [TimetableController::class, 'destroy'])
        ->name('timetables.destroy');
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

// Rutas para Excel a Word
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/excel-word', [ExcelWordController::class, 'index'])->name('excel-word.index');
    Route::post('/excel-word/process', [ExcelWordController::class, 'process'])->name('excel-word.process');
    Route::get('/excel-word/download/{filename}', [ExcelWordController::class, 'download'])->name('excel-word.download');
    Route::get('/excel-word/ejemplo', function () {
        return view('excel-word.ejemplo');
    })->name('excel-word.ejemplo');
});
