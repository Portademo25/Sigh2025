<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // Importante para programar

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// --- TAREA AUTOMÁTICA DE CUMPLEAÑOS ---
// Se ejecuta el primer día de cada mes a las 8:00 AM
Schedule::command('rrhh:enviar-cumpleanos')
    ->monthlyOn(1, '06:00')
    ->appendOutputTo(storage_path('logs/cumpleanos_automaticos.log'));
