<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot()
{
    Paginator::useBootstrapFive();

    // Importante: No ejecutes lógica de DB si estás en la consola (ej. corriendo migraciones)
    if ($this->app->runningInConsole()) {
        return;
    }

    try {
        // Establecemos una conexión por defecto para evitar que use la errónea de SIGESP aquí
        $db = DB::connection(); 

        // 1. LÓGICA DE SETTINGS
        if (Schema::hasTable('settings')) {
            $site_settings = $db->table('settings')->pluck('value', 'key')->all();
            view()->share('site_settings', $site_settings);

            if (isset($site_settings['app_name'])) {
                Config::set('app.name', $site_settings['app_name']);
            }

            if (isset($site_settings['expiracion_sesion'])) {
                Config::set('session.lifetime', (int)$site_settings['expiracion_sesion']);
            }
        }

        // 2. CONFIGURACIÓN DE CORREO SMTP
        if (Schema::hasTable('mail_settings')) {
            $mail = $db->table('mail_settings')->first();
            if ($mail) {
                config([
                    'mail.mailers.smtp.host' => $mail->host,
                    'mail.mailers.smtp.port' => $mail->port,
                    'mail.mailers.smtp.encryption' => $mail->encryption,
                    'mail.mailers.smtp.username' => $mail->username,
                    'mail.mailers.smtp.password' => $mail->password,
                    'mail.from.address' => $mail->from_address,
                    'mail.from.name' => $mail->from_name,
                ]);
            }
        }

    } catch (\Exception $e) {
        // Si la base de datos no conecta, el sistema no muere, solo loguea el error
        Log::error("Error en AppServiceProvider: " . $e->getMessage());
        
        // Compartimos un array vacío para que las vistas no den error de "variable no definida"
        view()->share('site_settings', []);
    }
}
}
