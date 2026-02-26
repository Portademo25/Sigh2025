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
use App\Http\Controllers\Empleado\ConstanciaController;
use App\Http\Controllers\Empleado\ArcController;
use App\Http\Controllers\Empleado\IvssController;
use App\Http\Controllers\Empleado\PerfilController;
use App\Http\Controllers\Admin\AdminReporteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\RRHH\DashboardrrhhController;
use App\Http\Controllers\RRHH\PersonalController;
use App\Http\Controllers\RRHH\CestaticketController;

Route::get('/', function () {
    return view('welcome');
});

 // Rutas públicas para verificación de documentos
    Route::get('/verificar/documento/{token}', [ConstanciaController::class, 'verificarPublico'])->name('constancia.verificar');

    Route::get('/verificar/arc/{token}', [ArcController::class, 'verificarArcPublico'])->name('arc.verificar');
    // Rutas de Onboarding
// 1. Ruta para verificar si el correo electrónico ya existe
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
       Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/locked', [UserController::class, 'lockedUsers'])->name('admin.users.locked');
        Route::post('/usuarios/{id}/desbloquear', [AdminUserController::class, 'unlockUser'])->name('admin.users.unlock');
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
        Route::get('/admin/reportes/constancias', [ConstanciaController::class, 'reporteAdmin'])->name('admin.reporte.constancias')->middleware('auth');
        Route::get('/admin/reportes', function () {return view('admin.reportes.menu');})->name('admin.reportes.menu')->middleware('auth');
        Route::get('/admin/reportes/arc', [ArcController::class, 'index'])->name('admin.reportes.arc');
        Route::get('/historial-descargas', [AdminReporteController::class, 'historialDescargas'])->name('admin.historial.descargas');
        Route::get('/admin/security', [SettingsController::class, 'securityIndex'])->name('admin.security.index');
        Route::post('/admin/security/action', [SettingsController::class, 'handleSecurityAction'])->name('admin.security.action');
        Route::get('/admin/security/policies', [SettingsController::class, 'policiesIndex'])->name('admin.security.policies');
        Route::post('/admin/security/policies/save', [SettingsController::class, 'updateSecurityPolicies'])->name('admin.security.policies.save');
        Route::get('/admin/exportar-reportes', [DashboardController::class, 'exportarExcel'])->name('admin.export.excel');
        Route::post('/admin/security/toggle-maintenance', [SettingsController::class, 'toggleMaintenance'])->name('admin.security.toggle-maintenance');
        Route::get('/settings/general', [SettingsController::class, 'generalIndex'])->name('admin.settings.general');
        Route::post('/settings/general/update', [SettingsController::class, 'updateGeneral'])->name('admin.settings.general.update');
        Route::post('/settings/test-sigesp', [SettingsController::class, 'testSigespConnection'])->name('admin.settings.test_sigesp');
        Route::post('/settings/test-local', [SettingsController::class, 'testLocalConnection'])->name('admin.settings.test_local');
        Route::get('/settings/fetch-users', [App\Http\Controllers\Admin\SettingsController::class, 'fetchUsers'])->name('admin.settings.fetch_users');
        Route::put('/admin/settings/users/{user}/update-email', [SettingsController::class, 'updateEmail'])->name('admin.users.update_email');
        Route::get('/admin/reportes/arc/generar/{cedper}/{ano}', [ArcController::class, 'generarArcAdmin'])->name('admin.arc.generar');
    });
// Rutas para empleados
    Route::middleware(['role:empleado'])->group(function () {
        Route::get('/empleado/dashboard', function () {return view('empleado.dashboard');})->name('empleado.dashboard');
        Route::get('/mi-perfil', [PerfilController::class, 'index'])->name('empleado.perfil');
        Route::get('/reportes', [EmpleadoController::class, 'menuReportes'])->name('empleado.reportes.menu');
        Route::get('/mis-recibos', [EmpleadoController::class, 'misRecibos'])->name('empleado.reportes.recibos');
        Route::get('/descargar-recibo/{codnom}/{codperi}', [EmpleadoController::class, 'descargarPDF'])->name('empleado.reportes.recibo_pdf');
        Route::get('/empleado/constancia', [ConstanciaController::class, 'pdfConstancia'])->name('empleado.reportes.constancia_pdf');
        Route::get('/empleado/reporte/arc/{ano}', [ArcController::class, 'generarArc'])->name('arc.pdf')->middleware('auth');
        Route::get('/empleado/reporte/arc', [ArcController::class, 'indexArc'])->name('empleado.reportes.arc_index')->middleware('auth');
        Route::get('/reportes/ivss', [IvssController::class, 'index'])->name('empleado.reportes.ivss_index');
        Route::get('/reportes/ivss/descargar/{ano}', [IvssController::class, 'generar14100'])->name('empleado.reportes.ivss_14100');
    });

    //Rutas para RRHH
    Route::middleware(['role:analista_rrhh'])->group(function () {
       Route::get('/rrhh/dashboard', [DashboardrrhhController::class, 'index'])->name('rrhh.dashboard');
       Route::get('/rrhh/personal', [PersonalController::class, 'index'])->name('rrhh.personal.index');
       Route::get('/rrhh/cestaticket', [CestaticketController::class, 'index'])->name('rrhh.cestaticket.index');
       Route::post('/rrhh/cestaticket/update', [CestaticketController::class, 'update'])->name('rrhh.cestaticket.update');
       Route::get('/rrhh/personal/arc/generar/{cedper}/{ano}', [PersonalController::class, 'generarARC'])->name('rrhh.arc.generar');
       Route::get('/rrhh/personal/pagos-listado', [PersonalController::class, 'listaPagos'])->name('rrhh.personal.pagos.index'); // Asegúrate que termine en .pagos.index
       Route::get('/rrhh/personal/pagos/{cedper}', [PersonalController::class, 'gestionarPagos'])->name('rrhh.personal.pagos');
       Route::post('/rrhh/personal/recibo/descargar', [PersonalController::class, 'descargarRecibo'])->name('rrhh.recibo.descargar');
       Route::get('/rrhh/personal/constancias', [PersonalController::class, 'listaConstancias'])->name('rrhh.personal.constancias.index');
       Route::get('/rrhh/personal/constancia/descargar/{cedper}', [PersonalController::class, 'descargarConstancia'])->name('rrhh.personal.constancia');
       Route::get('/rrhh/constancias/validar', [PersonalController::class, 'indexValidacion'])->name('rrhh.constancias.validar');
    });







    // Ruta común para ambos roles
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
});

