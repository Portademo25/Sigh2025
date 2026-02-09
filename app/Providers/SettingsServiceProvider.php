<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider; // <--- DEBE SER ESTE
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use App\Models\Setting;

class SettingsServiceProvider extends ServiceProvider
{
    // NO agregues un método __construct aquí.
    // Laravel se encarga de eso internamente.

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            if (Schema::hasTable('settings')) {
                $appName = Setting::where('key', 'app_name')->value('value');
                if ($appName) {
                    Config::set('app.name', $appName);
                }
            }
        } catch (\Exception $e) {
            // Previene errores durante la instalación o migraciones
        }
    }
}
