<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\RateLimiter; // Para limpiar el throttling de Laravel
use App\Models\Conexion;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function lockedUsers()
    {
        $lockedUsers = User::where('is_locked', true)->get();
        return view('admin.locked_users', compact('lockedUsers')); // Crea esta vista
    }
public function unlockUser($id)
    {
        // 1. Buscamos al usuario existente (findOrFail evita el error de INSERT)
        $user = User::findOrFail($id);

        // 2. Cambiamos el estatus en la base de datos
        $user->is_locked = false;
        $user->save(); // Esto ejecutará un UPDATE seguro

        // 3. Limpiamos el limitador de intentos (RateLimiter)
        // La llave debe coincidir con la del Login: usualmente "email|"
        $throttleKey = strtolower(trim($user->email)) . '|';
        RateLimiter::clear($throttleKey);

        // 4. Redirigir con mensaje de éxito
        return back()->with('success', "El usuario {$user->name} ha sido desbloqueado y ya puede intentar iniciar sesión.");
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

public function rolesIndex()
    {
        $usuarios = User::select('id', 'name', 'email', 'cedula', 'rol_id')
                        ->orderBy('name', 'asc')
                        ->paginate(15);

        return view('admin.settings.roles', compact('usuarios'));
    }


public function updateUserRole(Request $request, User $user)
    {
        // 1. Validación de los IDs que vimos en tu pgAdmin
        $request->validate([
            'rol_id' => 'required|in:1,2'
        ]);

        // 2. Seguridad: No quitarse permisos a uno mismo
        if ($user->id === Auth::id() && $request->rol_id != 1) {
            return back()->withErrors(['error' => 'No puedes revocarte los permisos de administrador.']);
        }

        try {
            // 3. Sincronización de Roles (Spatie + Tu columna manual)
            // Esto es lo que hace que el middleware 'role:admin' funcione
            $nombreRol = ($request->rol_id == 1) ? 'admin' : 'empleado';
            
            // Asignamos el rol en las tablas de Spatie (model_has_roles)
            $user->syncRoles([$nombreRol]);

            // Actualizamos tu columna manual en la tabla users
            $user->rol_id = $request->rol_id;

            // 4. Mantenimiento de Sesión (Para CheckSessionId)
            // Si el usuario soy yo, evito que el middleware global me saque
            if ($user->id === Auth::id()) {
                $user->current_session_id = session()->getId();
            }

            $user->save();

            // 5. Refrescar la instancia de Autenticación
            if ($user->id === Auth::id()) {
                // Forzamos a Laravel a cargar los nuevos roles y datos en memoria
                Auth::setUser($user->fresh());
            }

            // 6. Auditoría de Seguridad Sigh2025
            if (function_exists('record_security_event')) {
                record_security_event('Cambio de Privilegios', 'Alta', [
                    'usuario' => $user->cedula,
                    'nuevo_rol' => $nombreRol
                ]);
            }

            // 7. Redirección final
            if ($user->id === Auth::id() && $user->rol_id == 1) {
                return redirect()->route('admin.dashboard')->with('success', '¡Rol de Administrador activado correctamente!');
            }

            return back()->with('success', "Se ha asignado el rol de {$nombreRol} a {$user->name}.");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al sincronizar roles: ' . $e->getMessage()]);
        }
    }
}
