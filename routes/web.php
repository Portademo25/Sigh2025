<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;
use Illuminate\Support\Facades\Auth;

//ruta de inicio
Route::get('/', [InicioController::class, 'index'])->name('inicio');

Auth::routes();



//rutas publicas
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



// rutas para admin (acceso total)


// rutas para usuarios (acceso limitado por permisos)
