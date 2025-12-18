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
    protected $maxAttempts = 3;

    /**
     * La cantidad de minutos de espera después de la limitación de velocidad.
     * @var int
     */
    protected $decayMinutes = 5;

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
    // 1. Validar si el usuario existe y si está permanentemente bloqueado
    $this->validateLogin($request);

    // 2. Verificar si el usuario ha excedido el límite de intentos (3 fallos)
    if ($this->hasTooManyLoginAttempts($request)) {

        // --- LÓGICA DE BLOQUEO PERMANENTE ---
        // Si el usuario ya está 'throttled' (ha fallado 3 veces en 5 minutos),
        // aprovechamos para bloquearlo permanentemente.
        $user = User::where($this->username(), $request->{$this->username()})->first();
        if ($user && !$user->is_locked) {
            $user->is_locked = true;
            $user->save();
        }
        // ------------------------------------

        // Devolver la respuesta de bloqueo temporal de Laravel (5 minutos)
        $this->fireLockoutEvent($request);
        return $this->sendLockoutResponse($request);
    }

    // 3. Intentar autenticar (credenciales correctas)
    if ($this->attemptLogin($request)) {
        return $this->sendLoginResponse($request);
    }

    // 4. Si la autenticación falló (credenciales incorrectas):

    // Incrementar el contador de intentos fallidos de Laravel
    $this->incrementLoginAttempts($request);

    // --- LÓGICA DE BLOQUEO PERMANENTE AL ALCANZAR EL LÍMITE ---
    // Verificar si con este intento fallido se alcanzó o superó el límite ($maxAttempts = 3)
    if (RateLimiter::attempts($this->throttleKey($request)) >= $this->maxAttempts) {

        $user = User::where($this->username(), $request->{$this->username()})->first();
        if ($user && !$user->is_locked) {
            // Marcar como permanentemente bloqueado en la base de datos
            $user->is_locked = true;
            $user->save();
        }
    }
    // -----------------------------------------------------------

    // Devolver la respuesta de error de credenciales estándar
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

protected function incrementLoginAttempts(Request $request)
{
    $this->limiter()->hit($this->throttleKey($request), $this->decayMinutes());

    $user = \App\Models\User::where($this->username(), $request->{$this->username()})->first();

    if ($user) {
        // LEER EL VALOR DESDE LA BASE DE DATOS
        $maxAttempts = \App\Models\Setting::where('key', 'max_attempts')->value('value') ?? 3;

        if ($this->limiter()->attempts($this->throttleKey($request)) >= $maxAttempts) {
            $user->update(['is_locked' => true]);
            
            // Opcional: Registrar en el log
            Log::warning("Usuario bloqueado por exceder {$maxAttempts} intentos: " . $user->email);
        }
    }
}


}

