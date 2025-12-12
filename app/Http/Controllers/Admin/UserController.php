<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\RateLimiter; // Para limpiar el throttling de Laravel
use App\Models\Conexion;

class UserController extends Controller
{
    public function lockedUsers()
    {
        $lockedUsers = User::where('is_locked', true)->get();
        return view('admin.locked_users', compact('lockedUsers')); // Crea esta vista
    }

    public function unlockUser(User $user)
    {
        // 1. Quitar el bloqueo permanente
        $user->is_locked = false;
        $user->save();

        // 2. Limpiar el contador de intentos de login de Laravel para esta cuenta
        // Esto evita que el usuario quede "throttled" temporalmente incluso después de desbloquear
        RateLimiter::clear($user->email); // Laravel usa el nombre de usuario/email por defecto

        return redirect()->route('admin.users.locked')->with('success', 'Usuario desbloqueado con éxito.');
    }

    public function index()
    {
        $lockedUsersCount = User::where('is_locked', true)->count();
        $totalUsers = User::count();
        $recentConnections = Conexion::orderByDesc('created_at')->take(5)->get();

        return view('admin.index', compact('lockedUsersCount', 'totalUsers', 'recentConnections'));
    }

    /**
     * Muestra el historial completo de conexiones (IP y tiempo).
     */
    public function connectionHistory()
    {
        // Obtener todas las conexiones y paginarlas, cargando el usuario relacionado
        $connections = Conexion::with('user')
                               ->orderByDesc('created_at')
                               ->paginate(20);

        return view('admin.connection_history', compact('connections'));
    }
}
