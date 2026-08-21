<?php

use App\Http\Controllers\Blog\InlineImageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Dashboard\ImageUploadController;
use App\Http\Controllers\ServicesController;
use App\Livewire\Blog\PostForm;
use App\Livewire\Blog\PostsIndex;
use App\Livewire\Projects\ProjectsIndex;
use App\Livewire\Services\ServicesIndex;
use App\Livewire\Tags\TagsIndex;
use App\Livewire\Users\UsersIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages::home')->name('home');
Route::view('projects', 'pages::projects.index')->name('projects');

Route::get('blog', [BlogController::class, 'index'])->name('blog');
Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::prefix('about')->name('about.')->group(function () {
    Route::view('vision', 'pages::about.vision')->name('vision');
    Route::view('mission', 'pages::about.mission')->name('mission');
});

Route::middleware(['auth', 'verified', 'role'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth', 'verified', 'can:manage-posts'])->prefix('dashboard/blog')->name('blog.')->group(function () {
    Route::get('/', PostsIndex::class)->name('index');
    Route::get('create', PostForm::class)->name('create');
    Route::post('images', InlineImageController::class)->middleware('throttle:blog-inline-images')->name('images.store');
    Route::get('{post}/edit', PostForm::class)->whereNumber('post')->name('edit');
});

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('users', UsersIndex::class)->name('users.index');
    Route::get('projects', ProjectsIndex::class)->name('projects.index');
    Route::get('services', ServicesIndex::class)->name('services.index');
    Route::get('tags', TagsIndex::class)->name('tags.index');
    Route::post('images', ImageUploadController::class)->middleware('throttle:dashboard-images')->name('images.store');
});

require __DIR__.'/settings.php';

// *public routes services
Route::get('services', [ServicesController::class, 'index'])->name('services');
Route::get('services/{slug}', [ServicesController::class, 'show'])->name('services.show');
