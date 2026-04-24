<?php

use App\Models\SecurityLog;
use Illuminate\Support\Facades\Auth;

if (!function_exists('record_security_event')) {
    /**
     * Registra un evento de seguridad en la bitácora global.
     *
     * @param string $event Nombre del evento
     * @param string $severity Baja, Media, Alta, Crítica
     * @param array|null $details Detalles adicionales en formato array
     * @return void
     */
    function record_security_event($event, $severity = 'Baja', $details = null)
    {
        SecurityLog::create([
            'event'           => $event,
            'user_identifier' => Auth::user()->cedper ?? Auth::user()->name ?? 'Sistema/Invitado',
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
            'severity'        => $severity,
            'details'         => $details,
        ]);
    }
}