<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\LibraryItemController;
use App\Http\Controllers\LibraryAccessController;
use App\Http\Middleware\AdminMiddleware;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Dashboard Route - Auth Only (temporarily relaxed from admin-only)
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Library Access Routes
Route::get('/library/access', [LibraryAccessController::class, 'showAccessForm'])->name('library.access');
Route::post('/library/access', [LibraryAccessController::class, 'processAccess'])->name('library.process-access');
Route::get('/library/access/{token}', [LibraryAccessController::class, 'accessWithToken'])->name('library.access-token');
Route::post('/library/revoke', [LibraryAccessController::class, 'revokeAccess'])->name('library.revoke-access');

// Test route for direct view access - No middleware
Route::get('/library/items/create/test', function() {
    return view('library.create');
})->name('library.create.test');

// Admin Routes for Library Management - Temporarily Auth Only instead of Admin Only
Route::middleware(['auth'])->group(function () {
    // Library Item Management - Auth Required (temporarily relaxed from admin-only)
    Route::get('/library/items/create', [LibraryItemController::class, 'create'])->name('library.create');
    Route::post('/library/items', [LibraryItemController::class, 'store'])->name('library.store');
    Route::get('/library/items/{id}/edit', [LibraryItemController::class, 'edit'])->name('library.edit');
    Route::put('/library/items/{id}', [LibraryItemController::class, 'update'])->name('library.update');
    Route::delete('/library/items/{id}', [LibraryItemController::class, 'destroy'])->name('library.destroy');

    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

// Public Library Routes - No Auth Required
Route::get('/library', [LibraryItemController::class, 'index'])->name('library.index');
Route::get('/library/items/{id}', [LibraryItemController::class, 'show'])->name('library.show');
Route::get('/library/download/{id}', [LibraryItemController::class, 'download'])->name('library.download');
Route::get('/library/category/{category}', [LibraryItemController::class, 'filterByCategory'])->name('library.category');
Route::get('/library/tag/{tag}', [LibraryItemController::class, 'filterByTag'])->name('library.tag');

require __DIR__.'/auth.php';
