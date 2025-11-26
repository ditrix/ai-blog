<?php

use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [PostController::class, 'index'])->name('posts.index');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';

Route::middleware(['auth', 'verified'])->prefix('adm')->name('admin.')->group(function () {
    Route::get('/', [AdminPostController::class, 'index'])->name('posts.index');
    Route::resource('posts', AdminPostController::class)->except(['show']);
});

Route::get('/{slug}', [PostController::class, 'show'])->name('posts.show');
