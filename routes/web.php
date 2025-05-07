<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/upload', [FileUploadController::class, 'process'])->name('upload.file');
Route::get('/download-template', [FileUploadController::class, 'download'])->name('download.template');
