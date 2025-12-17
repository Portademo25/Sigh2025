<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckSessionId
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Si el ID en DB es nulo o no coincide con la sesión actual
            if ($user->current_session_id !== $request->session()->getId()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Tu sesión ha sido finalizada por un administrador.');
            }
        }
        return $next($request);
    }
}
