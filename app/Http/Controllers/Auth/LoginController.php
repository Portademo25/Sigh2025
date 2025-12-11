<?php
// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    // IMPORTANTE: Cambiar la propiedad $redirectTo
    // protected $redirectTo = '/home'; // ← Comentar o eliminar esta línea
    // Añadir la propiedad para evitar el error "Undefined property '$redirectTo'"
    protected $redirectTo = '/home';
    
    // En su lugar, usar el método authenticated()

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Sobrescribir el método authenticated
     * Este método se ejecuta después de un login exitoso
     */
    protected function authenticated(Request $request, $user)
    {
        // Verificar si el usuario tiene algún rol
        if ($user->hasRole('administrador')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('empleado')) {
            return redirect()->route('empleado.dashboard');
        }

        // Redirección por defecto si no tiene rol asignado
        return redirect('/home');
    }

    /**
     * Sobrescribir el método redirectPath para evitar conflictos
     */
    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        if (isset($this->redirectTo)) {
            return $this->redirectTo;
        }

        return '/home';
    }
}
