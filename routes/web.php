<?php

use App\Http\Controllers\ServicesController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages::home')->name('home');
Route::view('projects', 'pages::projects')->name('projects');
Route::view('blog', 'pages::blog')->name('blog');

Route::prefix('about')->name('about.')->group(function () {
    Route::view('vision', 'pages::about-vision')->name('vision');
    Route::view('mission', 'pages::about-mission')->name('mission');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';

// *public routes services
Route::get('services', [ServicesController::class, 'index'])->name('services');
Route::get('services/{slug}', [ServicesController::class, 'show'])->name('services.show');
