<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Añadimos DB
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator; // <--- No olvides importar esto

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {

        Paginator::useBootstrapFive();
        try {
            // 1. LÓGICA EXISTENTE: Nombre de la App y Estatus (Tabla 'settings')
            if (Schema::hasTable('settings')) {
                $settings = Setting::whereIn('key', ['app_name', 'site_offline'])->get()->keyBy('key');

                if (isset($settings['app_name'])) {
                    Config::set('app.name', $settings['app_name']->value);
                }
            }

            // 2. NUEVA LÓGICA: Configuración de Correo SMTP (Tabla 'mail_settings')
            if (Schema::hasTable('mail_settings')) {
                $mail = DB::table('mail_settings')->first();

                if ($mail) {
                    Config::set('mail.mailers.smtp.host', $mail->host);
                    Config::set('mail.mailers.smtp.port', $mail->port);
                    Config::set('mail.mailers.smtp.encryption', $mail->encryption);
                    Config::set('mail.mailers.smtp.username', $mail->username);
                    Config::set('mail.mailers.smtp.password', $mail->password);

                    // Configuración del remitente (quién envía el correo)
                    Config::set('mail.from.address', $mail->from_address);
                    Config::set('mail.from.name', $mail->from_name);
                }
            }

        } catch (\Exception $e) {
            // Evita que la app rompa si las tablas aún no existen (ej: durante migraciones)
            Log::error("Error cargando configuraciones desde DB: " . $e->getMessage());
        }
    }
}
