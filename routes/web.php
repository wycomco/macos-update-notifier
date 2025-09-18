<?php

use App\Http\Controllers\Auth\MagicLinkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicSubscriberController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\SubscriberImportController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Magic Link Authentication
Route::middleware('guest')->group(function () {
    Route::get('/auth/magic-link', [MagicLinkController::class, 'create'])->name('magic-link.form');
    Route::post('/auth/magic-link', [MagicLinkController::class, 'store'])
        ->middleware('throttle:5,1') // 5 requests per minute
        ->name('magic-link.request');
});
Route::get('/auth/magic-link/verify/{user}', [MagicLinkController::class, 'verify'])->name('magic-link.verify');
Route::get('/auth/magic-link/verify-new', [MagicLinkController::class, 'verifyNew'])->name('magic-link.verify-new');

// Public subscriber actions (no auth required)
Route::get('/unsubscribe/{token}', [PublicSubscriberController::class, 'showUnsubscribe'])->name('public.unsubscribe');
Route::post('/unsubscribe/{token}', [PublicSubscriberController::class, 'unsubscribe'])->name('public.unsubscribe.confirm');
Route::get('/change-version/{token}', [PublicSubscriberController::class, 'showVersionChange'])->name('public.version-change');
Route::post('/change-version/{token}', [PublicSubscriberController::class, 'changeVersion'])->name('public.version-change.update');
Route::get('/change-language/{token}', [PublicSubscriberController::class, 'showLanguageChange'])->name('public.language-change');
Route::post('/change-language/{token}', [PublicSubscriberController::class, 'changeLanguage'])->name('public.language-change.update');

// Success pages for public subscriber actions
Route::get('/unsubscribed/{token}', [PublicSubscriberController::class, 'showUnsubscribed'])->name('public.unsubscribed');
Route::get('/language-changed/{token}', [PublicSubscriberController::class, 'showLanguageChanged'])->name('public.language-changed');
Route::get('/version-changed/{token}', [PublicSubscriberController::class, 'showVersionChanged'])->name('public.version-changed');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');
    Route::get('/dashboard/super-admin', [DashboardController::class, 'superAdmin'])->middleware('super-admin')->name('dashboard.super-admin');
    
    // Subscriber management
    Route::resource('subscribers', SubscriberController::class);
    Route::get('/subscribers/{subscriber}/resubscribe', [SubscriberController::class, 'showResubscribe'])->name('subscribers.resubscribe.form');
    Route::post('/subscribers/{subscriber}/resubscribe', [SubscriberController::class, 'resubscribe'])->name('subscribers.resubscribe');
    
    // Subscriber import (available to all authenticated users)
    Route::get('/subscribers-import', [SubscriberImportController::class, 'create'])->name('subscribers.import');
    Route::post('/subscribers-import', [SubscriberImportController::class, 'store'])->name('subscribers.import.process');
    
    // Super admin only routes
    Route::middleware('super-admin')->group(function () {
        // Add any super admin specific routes here if needed
    });
    
    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Super admin only routes
Route::middleware(['auth', 'super-admin'])->group(function () {
    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/{user}', [UserManagementController::class, 'show'])->name('admin.users.show');
    Route::post('/admin/users/{user}/promote', [UserManagementController::class, 'promoteToSuperAdmin'])->name('admin.users.promote');
    Route::post('/admin/users/{user}/demote', [UserManagementController::class, 'demoteFromSuperAdmin'])->name('admin.users.demote');
    Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
});

require __DIR__.'/auth.php';
