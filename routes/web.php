<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'pages::home')->name('home');
Route::view('services', 'pages::services')->name('services');
Route::view('projects', 'pages::projects')->name('projects');
Route::view('blog', 'pages::blog')->name('blog');

Route::view('about', 'pages::about')->name('about');
Route::prefix('about')->name('about.')->group(function () {
    Route::view('vision', 'pages::about-vision')->name('vision');
    Route::view('mission', 'pages::about-mission')->name('mission');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
