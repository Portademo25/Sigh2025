<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\OnboardingController;
use App\Http\Controllers\EmpleadoController;


Route::get('/', function () {
    return view('welcome');
});



  Route::post('/auth/check-email', [OnboardingController::class, 'checkEmail'])->name('auth.check_email');

// 2. Ruta para mostrar el formulario de "Terminar Registro" (protegida por firma para seguridad básica)
    Route::get('/auth/complete-registration', [OnboardingController::class, 'showRegisterForm'])->name('auth.complete_register');

// 3. Ruta para guardar el usuario final en la tabla users
    Route::post('/auth/store-user', [OnboardingController::class, 'storeUser'])->name('auth.store_user');

Auth::routes();

    Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Rutas para administradores
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', function () {return view('admin.dashboard');})->name('admin.dashboard');
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
        Route::get('/settings/mail', [SettingsController::class, 'editMailSettings'])->name('admin.settings.mail');
        Route::post('/settings/correo', [SettingsController::class, 'updateMailSettings'])->name('admin.mail.update');
        Route::post('/settings/correo/test', [SettingsController::class, 'testMailSettings'])->name('admin.mail.test');
        Route::get('/settings/roles', [AdminUserController::class, 'rolesIndex'])->name('admin.settings.roles');
        Route::post('/settings/roles/{user}', [AdminUserController::class, 'updateUserRole'])->name('admin.settings.roles.update');
    });

    // Rutas para empleados
    Route::middleware(['role:empleado'])->group(function () {
        Route::get('/empleado/dashboard', function () {return view('empleado.dashboard');})->name('empleado.dashboard');
        Route::get('/empleado/profile', function () {return view('empleado.profile');})->name('empleado.profile');
        Route::get('/reportes', [EmpleadoController::class, 'menuReportes'])->name('empleado.reportes.menu');
        Route::get('/mis-recibos', [EmpleadoController::class, 'misRecibos'])->name('empleado.reportes.recibos');
        Route::get('/descargar-recibo/{codnom}/{codperi}', [EmpleadoController::class, 'descargarPDF'])->name('empleado.reportes.recibo_pdf');
    });

    // Ruta común para ambos roles
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
});
