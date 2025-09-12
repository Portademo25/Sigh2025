<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;

//ruta de inicio
Route::get('/', [InicioController::class, 'index'])->name('inicio');

Auth::routes();



//rutas publicas
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



// rutas para admin (acceso total)
Route::prefix('Administrador')->middleware(['auth','role:Administrador'])->group(function(){
    Route::get('/dashboard', [HomeController::class,'adminDashboard'])->name('admin.dashboard');
});


// rutas para talento humano (acceso limitado por permisos)
Route::prefix('TalentoHumano')->middleware(['auth','role:Telento Humano'])->group(function(){
    Route::get('/dashboard', [HomeController::class,'talenthumanDashboard'])->name('talenthuman.dashboard');
});
// rutas para empleado (acceso limitado por permisos)
Route::prefix('Empleado')->middleware(['auth','role:Empleado'])->group(function(){
    Route::get('/dashboard', [HomeController::class,'employeeDashboard'])->name('employee.dashboard');
});