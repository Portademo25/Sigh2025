<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Punto de entrada principal después del Login.
     */
    public function index()
    {
        return $this->redirectBasedOnRole();
    }

    /**
     * Ruta /dashboard que redirige según el rol.
     */
    public function dashboard()
    {
        return $this->redirectBasedOnRole();
    }

    /**
     * Lógica centralizada de redirección para evitar repetición.
     */
    private function redirectBasedOnRole()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('empleado')) {
            return redirect()->route('empleado.dashboard');
        }

        if ($user->hasRole('analista_rrhh')) {
            return redirect()->route('rrhh.dashboard');
        }
        // Si por alguna razón no tiene rol, enviarlo a una vista genérica o cerrar sesión
        return view('home');
    }
}
