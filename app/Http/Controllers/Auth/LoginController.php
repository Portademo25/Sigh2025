<?php
// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request; // ¡Asegúrate de importar Request!
use App\Models\User; // ¡Asegúrate de importar tu modelo User!
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Conexion;
use Illuminate\Support\Facades\DB;


class LoginController extends Controller
{
    use AuthenticatesUsers;

    // IMPORTANTE: Cambiar la propiedad $redirectTo
    // protected $redirectTo = '/home'; // ← Comentar o eliminar esta línea
    // Añadir la propiedad para evitar el error "Undefined property '$redirectTo'"
    protected $redirectTo = '/home';

    /**
     * El número máximo de intentos de inicio de sesión permitidos.
     * @var int
     */


    /**
     * La cantidad de minutos de espera después de la limitación de velocidad.
     * @var int
     */


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
        if ($user->hasRole('admin')) {
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

  public function login(Request $request)
{
    $this->validateLogin($request);

    // Si el usuario ya tiene bloqueo temporal de Laravel (RateLimiter)
    if ($this->hasTooManyLoginAttempts($request)) {
        $this->fireLockoutEvent($request);
        return $this->sendLockoutResponse($request);
    }

    // Intentar entrar
    if ($this->attemptLogin($request)) {
        return $this->sendLoginResponse($request);
    }

    // SI FALLA: Incrementar intentos
    $this->incrementLoginAttempts($request);

    // Lógica de bloqueo en BD al alcanzar el límite
    $user = User::where($this->username(), $request->{$this->username()})->first();
    if ($user) {
        $maxAttempts = \App\Models\Setting::where('key', 'max_attempts')->value('value') ?? 3;

        if ($this->limiter()->attempts($this->throttleKey($request)) >= $maxAttempts) {
            $user->update(['is_locked' => true]);
            Log::warning("Bloqueo definitivo para: " . $user->email);
        }
    }

    return $this->sendFailedLoginResponse($request);
}

protected function sendLoginResponse(Request $request)
{
    $request->session()->regenerate();
    $user = Auth::user();

    // LOG DE PRUEBA: Revisa storage/logs/laravel.log después de intentar loguearte
    Log::info('Intento de registro de conexión para: ' . $user->email);

    try {
        Conexion::create([
           'user_id'       => $user->id,
           'fechaconexion' => now()->format('Y-m-d'), // Fecha (Año-Mes-Día)
           'horaconexion'  => now()->format('H:i:s'), // Hora (Hora:Minuto:Segundo)
           'ipconexion'    => $request->ip(),
        ]);
    } catch (\Exception $e) {
        Log::error('Error guardando conexión: ' . $e->getMessage());
    }

    $user->current_session_id = $request->session()->getId();
    $user->save();
     $this->clearLoginAttempts($request);
    return $this->authenticated($request, $user) ?: redirect()->intended($this->redirectPath());
}

protected function validateLogin(Request $request)
{
    $request->validate([
        $this->username() => 'required|string',
        'password' => 'required|string',
    ]);

    // BUSCAR SI EL USUARIO ESTÁ BLOQUEADO PERMANENTEMENTE
    $user = User::where($this->username(), $request->{$this->username()})->first();

    if ($user && $user->is_locked) {
        throw ValidationException::withMessages([
            $this->username() => ['Su cuenta ha sido bloqueada permanentemente por seguridad. Contacte al administrador.'],
        ]);
    }
}


protected function incrementLoginAttempts(Request $request)
{
    // 1. Registramos el "golpe" en el limitador de Laravel
    $this->limiter()->hit($this->throttleKey($request), $this->decayMinutes() * 60);

    // 2. Buscamos al usuario por el email que intentó ingresar
    $email = strtolower(trim($request->input('email')));

    // IMPORTANTE: Buscamos el registro existente.
    // Si no existe, NO creamos uno nuevo.
    $user = \App\Models\User::where('email', $email)->first();

    if ($user) {
        // Obtenemos el máximo de intentos (por defecto 3)
        $maxAttempts = \App\Models\Setting::where('key', 'max_attempts')->value('value') ?? 3;
        $attempts = $this->limiter()->attempts($this->throttleKey($request));

        // Si llegó al límite, bloqueamos
        if ($attempts >= $maxAttempts) {
            // Al venir de un 'first()', Laravel sabe que debe hacer un UPDATE, no un INSERT
            $user->is_locked = true;
            $user->save();

            Log::warning("Cuenta bloqueada por seguridad: " . $user->email);
        }
    }
    // Si el usuario no existe en la tabla local, no hacemos nada.
    // Laravel manejará el error de "Credenciales incorrectas" normalmente.
}

    protected function maxAttempts()
    {
        // Busca 'intentos_maximos' en la tabla, si no existe usa 3 por defecto
        return DB::table('settings')->where('key', 'intentos_maximos')->value('value') ?? 3;
    }
    protected function decayMinutes()
    {
    // Busca 'duracion_bloqueo' en la tabla, si no existe usa 15 minutos
    return DB::table('settings')->where('key', 'duracion_bloqueo')->value('value') ?? 15;
    }
}

