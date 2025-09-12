<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

      public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        // Busca al usuario por su email
        $user = User::where('email', $credentials['email'])->first();

        // Si el usuario existe y está bloqueado, lanza una excepción
        if ($user && $user->locked_at !== null) {
            throw ValidationException::withMessages([
                'email' => 'Tu cuenta ha sido bloqueada debido a demasiados intentos fallidos. Contacta al administrador para desbloquearla.',
            ]);
        }

        // Intenta autenticar al usuario
        if (Auth::attempt($credentials)) {
            // Si el login es exitoso, reinicia los intentos fallidos y el bloqueo permanente
            if ($user) {
                $user->failed_attempts = 0;
                $user->locked_at = null;
                $user->save();
            }
            $request->session()->regenerate();
            return redirect()->intended($this->redirectTo);
        }

        // Si el login falla, incrementa el contador de intentos fallidos
        if ($user) {
            $user->failed_attempts++;

            // Si los intentos fallidos llegan a 3, bloquea la cuenta permanentemente
            if ($user->failed_attempts >= 3) {
                $user->locked_at = now();
                $user->save();
                throw ValidationException::withMessages([
                    'email' => 'Ha excedido el límite de intentos. Su cuenta ha sido bloqueada permanentemente.',
                ]);
            }
            
            $user->save();
        }

        // Lanza la excepción de validación para mostrar el error genérico
        throw ValidationException::withMessages([
            'email' => 'Las credenciales proporcionadas no son válidas.',
        ]);
    }
}
