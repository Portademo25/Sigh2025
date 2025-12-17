<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UpdateUserLastSeen
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            // Actualizamos la última vez que fue visto
            User::where('id', Auth::id())->update(['last_seen_at' => now()]);
        }
        return $next($request);
    }
}
