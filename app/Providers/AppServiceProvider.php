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

        try {
            // 1. LÓGICA DE SETTINGS (App Name, Offline y Expiración de Sesión)
            if (Schema::hasTable('settings')) {
                // Buscamos todas las llaves necesarias en una sola consulta para ahorrar recursos
                $settings = Setting::whereIn('key', [
                    'app_name',
                    'site_offline',
                    'expiracion_sesion'
                ])->get()->keyBy('key');

                // Configurar Nombre de la App
                if (isset($settings['app_name'])) {
                    Config::set('app.name', $settings['app_name']->value);
                }

                // NUEVO: Configurar Expiración de Sesión Dinámica
                if (isset($settings['expiracion_sesion'])) {
                    Config::set('session.lifetime', (int)$settings['expiracion_sesion']->value);
                    // Nota: Esto sobrescribe el valor de config/session.php en cada carga
                }
            }

            // 2. CONFIGURACIÓN DE CORREO SMTP (Tabla 'mail_settings')
            if (Schema::hasTable('mail_settings')) {
                $mail = DB::table('mail_settings')->first();

                if ($mail) {
                    Config::set('mail.mailers.smtp.host', $mail->host);
                    Config::set('mail.mailers.smtp.port', $mail->port);
                    Config::set('mail.mailers.smtp.encryption', $mail->encryption);
                    Config::set('mail.mailers.smtp.username', $mail->username);
                    Config::set('mail.mailers.smtp.password', $mail->password);
                    Config::set('mail.from.address', $mail->from_address);
                    Config::set('mail.from.name', $mail->from_name);
                }
            }

        } catch (\Exception $e) {
            // Logueamos el error pero permitimos que la app siga cargando
            Log::error("Error cargando configuraciones dinámicas: " . $e->getMessage());
        }
    }
}
