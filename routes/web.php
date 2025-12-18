<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SettingsController;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

    Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Rutas para administradores
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
        Route::get('users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/locked', [UserController::class, 'lockedUsers'])->name('admin.users.locked');
        Route::post('/users/{user}/unlock', [UserController::class, 'unlockUser'])->name('admin.users.unlock');
        Route::get('/users/connections', [AdminUserController::class, 'connectionHistory'])->name('admin.users.connections');
        Route::get('/users/active', [AdminUserController::class, 'activeUsers'])->name('admin.users.active');
        Route::post('/users/{user}/kick', [AdminUserController::class, 'kickUser'])->name('admin.users.kick');
        Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
        Route::post('/settings/update', [SettingsController::class, 'update'])->name('admin.settings.update');
        Route::get('/sigesp', [SettingsController::class, 'sigesp'])->name('admin.settings.sigesp');
        Route::post('/sigesp/sync', [SettingsController::class, 'syncSigesp'])->name('admin.settings.sigesp.sync');

});




    // Rutas para empleados
    Route::middleware(['role:empleado'])->group(function () {
        Route::get('/empleado/dashboard', function () {
            return view('empleado.dashboard');
        })->name('empleado.dashboard');

        Route::get('/empleado/profile', function () {
            return view('empleado.profile');
        })->name('empleado.profile');
    });

    // Ruta común para ambos roles
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
});
