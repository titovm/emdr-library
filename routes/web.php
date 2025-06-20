<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\LibraryItemController;
use App\Http\Controllers\LibraryAccessController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\LanguageController;
use App\Http\Middleware\AdminMiddleware;

// Language switch route
Route::get('/language/{locale}', [LanguageController::class, 'switchLanguage'])->name('language.switch');

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Admin Dashboard Route - Admin Only
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', AdminMiddleware::class])
    ->name('dashboard');

// Library Access Routes - For public users
Route::get('/library/access', [LibraryAccessController::class, 'showAccessForm'])->name('library.access');
Route::post('/library/access', [LibraryAccessController::class, 'processAccess'])->name('library.process-access');
Route::get('/library/access/{token}', [LibraryAccessController::class, 'accessWithToken'])->name('library.access-token');
Route::post('/library/revoke', [LibraryAccessController::class, 'revokeAccess'])->name('library.revoke-access');

// Admin Routes for Library Management
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->group(function () {
    // Library Item Management - Admin Only
    Route::get('/library/items/create', [LibraryItemController::class, 'create'])->name('library.create');
    Route::post('/library/items', [LibraryItemController::class, 'store'])->name('library.store');
    Route::get('/library/items/{id}/edit', [LibraryItemController::class, 'edit'])->name('library.edit');
    Route::put('/library/items/{id}', [LibraryItemController::class, 'update'])->name('library.update');
    Route::delete('/library/items/{id}', [LibraryItemController::class, 'destroy'])->name('library.destroy');
    
    // Categories and Tags Management - Admin Only
    Route::get('/taxonomy', [\App\Http\Controllers\AdminTaxonomyController::class, 'index'])->name('admin.taxonomy.index');
    Route::post('/taxonomy/categories', [\App\Http\Controllers\AdminTaxonomyController::class, 'storeCategory'])->name('admin.taxonomy.categories.store');
    Route::put('/taxonomy/categories', [\App\Http\Controllers\AdminTaxonomyController::class, 'updateCategory'])->name('admin.taxonomy.categories.update');
    Route::delete('/taxonomy/categories', [\App\Http\Controllers\AdminTaxonomyController::class, 'destroyCategory'])->name('admin.taxonomy.categories.destroy');
    Route::post('/taxonomy/tags', [\App\Http\Controllers\AdminTaxonomyController::class, 'storeTag'])->name('admin.taxonomy.tags.store');
    Route::put('/taxonomy/tags', [\App\Http\Controllers\AdminTaxonomyController::class, 'updateTag'])->name('admin.taxonomy.tags.update');
    Route::delete('/taxonomy/tags', [\App\Http\Controllers\AdminTaxonomyController::class, 'destroyTag'])->name('admin.taxonomy.tags.destroy');
    
    // Statistics Dashboard - Admin Only
    Route::get('/stats', [StatsController::class, 'index'])->name('admin.stats');
});

// User Setting Routes - Auth Only
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

// Public Library Routes - No Auth Required
Route::get('/library', [LibraryItemController::class, 'index'])->name('library.index');
Route::get('/library/items/{id}', [LibraryItemController::class, 'show'])->name('library.show');
Route::get('/library/download/{id}', [LibraryItemController::class, 'download'])->name('library.download'); // Legacy route for backward compatibility
Route::get('/library/files/{fileId}/download', [LibraryItemController::class, 'downloadFile'])->name('library.file.download');
Route::get('/library/category/{category}', [LibraryItemController::class, 'filterByCategory'])->name('library.category');
Route::get('/library/tag/{tag}', [LibraryItemController::class, 'filterByTag'])->name('library.tag');

require __DIR__.'/auth.php';
