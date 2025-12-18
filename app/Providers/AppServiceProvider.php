<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

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
    // Forzamos la lectura de la base de datos
    try {
        if (Schema::hasTable('settings')) {
            $settings = Setting::whereIn('key', ['app_name', 'site_offline'])->get()->keyBy('key');

            if (isset($settings['app_name'])) {
                Config::set('app.name', $settings['app_name']->value);
            }
        }
    } catch (\Exception $e) {
        // Ignorar errores durante migraciones
    }
}
}
