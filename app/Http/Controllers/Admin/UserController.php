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

            // Contar usuarios activos (últimos 5 min)
             $activeUsersCount = User::where('last_seen_at', '>=', now()->subMinutes(5))->count();

            $recentConnections = Conexion::with('user')->orderByDesc('created_at')->take(5)->get();

             return view('admin.index', compact('lockedUsersCount', 'totalUsers', 'recentConnections', 'activeUsersCount'));
    }

    /**
     * Muestra el historial completo de conexiones (IP y tiempo).
     */
    public function connectionHistory(Request $request)
    {
        // Obtener todas las conexiones y paginarlas, cargando el usuario relacionado
        $search = $request->input('search');


          $connections = Conexion::with('user')
        ->when($search, function ($query, $search) {
            return $query->where('ipconexion', 'LIKE', "%{$search}%")
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
        })
        ->orderByDesc('created_at')
        ->paginate(20)
        ->withQueryString(); // ¡Importante! Esto mantiene el filtro al cambiar de página

    return view('admin.connection_history', compact('connections', 'search'));
    }

    public function activeUsers()
{
    // Definimos que "activo" es haber tenido actividad en los últimos 5 minutos
    $activeThreshold = now()->subMinutes(5);

    $users = User::where('last_seen_at', '>=', $activeThreshold)
                 ->orderByDesc('last_seen_at')
                 ->get();

    return view('admin.active_users', compact('users'));
}


    public function kickUser(User $user)
{
    // Al poner esto en null, el Middleware CheckSessionId cerrará su sesión en el próximo clic
    $user->current_session_id = null;
    $user->save();

    return redirect()->back()->with('success', "El usuario {$user->name} ha sido expulsado del sistema.");
}
}
