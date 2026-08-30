<?php

use App\Http\Controllers\Blog\InlineImageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Dashboard\ImageUploadController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\SitemapController;
use App\Livewire\Blog\PostForm;
use App\Livewire\Blog\PostsIndex;
use App\Livewire\Projects\ProjectForm;
use App\Livewire\Projects\ProjectsIndex;
use App\Livewire\Services\ServiceForm;
use App\Livewire\Services\ServicesIndex;
use App\Livewire\Tags\TagForm;
use App\Livewire\Tags\TagsIndex;
use App\Livewire\Users\UserForm;
use App\Livewire\Users\UsersIndex;
use Illuminate\Support\Facades\Route;

Route::get('sitemap.xml', SitemapController::class)->name('sitemap');
Route::view('/', 'pages::home')->name('home');
Route::redirect(
    '/portafolio',
    'https://drive.google.com/file/d/1PL4N41EoJhcPEcnnm1OoOX8hHQPykDRw/view?usp=sharing'
);
Route::get('proyectos', [ProjectsController::class, 'index'])->name('projects');
Route::get('proyectos/{slug}', [ProjectsController::class, 'show'])->name('projects.show');

Route::get('blog', [BlogController::class, 'index'])->name('blog');
Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::prefix('about')->name('about.')->group(function () {
    Route::view('vision', 'pages::about.vision')->name('vision');
    Route::view('mission', 'pages::about.mission')->name('mission');
});

Route::view('politica-de-privacidad', 'pages::privacy.policy')->name('privacy.policy');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('pending-approval', 'pending-approval')->name('pending.approval');
});

Route::middleware(['auth', 'verified', 'pending'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth', 'verified', 'pending', 'can:manage-posts'])->prefix('dashboard/blog')->name('blog.')->group(function () {
    Route::get('/', PostsIndex::class)->name('index');
    Route::get('create', PostForm::class)->name('create');
    Route::post('images', InlineImageController::class)->middleware('throttle:blog-inline-images')->name('images.store');
    Route::get('{post}/edit', PostForm::class)->whereNumber('post')->name('edit');
});

Route::middleware(['auth', 'verified', 'pending', 'can:admin'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('users', UsersIndex::class)->name('users.index');
    Route::get('users/create', UserForm::class)->name('users.create');
    Route::get('users/{user}/edit', UserForm::class)->whereNumber('user')->name('users.edit');
    Route::get('projects', ProjectsIndex::class)->name('projects.index');
    Route::get('projects/create', ProjectForm::class)->name('projects.create');
    Route::get('projects/{project}/edit', ProjectForm::class)->whereNumber('project')->name('projects.edit');
    Route::get('services', ServicesIndex::class)->name('services.index');
    Route::get('services/create', ServiceForm::class)->name('services.create');
    Route::get('services/{service}/edit', ServiceForm::class)->whereNumber('service')->name('services.edit');
    Route::get('tags', TagsIndex::class)->name('tags.index');
    Route::get('tags/create', TagForm::class)->name('tags.create');
    Route::get('tags/{tag}/edit', TagForm::class)->whereNumber('tag')->name('tags.edit');
});

Route::middleware(['auth', 'verified', 'pending'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::post('images', ImageUploadController::class)->middleware('throttle:dashboard-images')->name('images.store');
});

require __DIR__.'/settings.php';

Route::get('servicios', [ServicesController::class, 'index'])->name('services');
Route::get('servicios/{slug}', [ServicesController::class, 'show'])->name('services.show');
