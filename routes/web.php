<?php

use App\Http\Controllers\Admin\VersionPreviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->get('/admin/versions/preview/{version?}', VersionPreviewController::class)
    ->name('admin.versions.preview');
