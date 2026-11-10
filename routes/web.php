<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\TenantController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('tenant.dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::view('/', 'admin.dashboard')->name('dashboard');
    });

    Route::middleware('role:tenant')->prefix('tenant')->name('tenant.')->group(function () {
        Route::view('/', 'tenant.dashboard')->name('dashboard');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::resource('rooms', RoomController::class)->except(['show', 'destroy']);
    });
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::resource('rooms', RoomController::class)->except(['show', 'destroy']);
    Route::resource('tenants', TenantController::class)->only(['index', 'create', 'store']);
    Route::patch('tenants/{tenant}/move-out', [TenantController::class, 'moveOut'])->name('tenants.move-out');
    });
});

require __DIR__.'/auth.php';