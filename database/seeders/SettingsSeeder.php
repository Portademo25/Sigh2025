<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'key'   => 'app_name',
                'value' => 'Sistema de Gestión de RRHH',
            ],
            [
                'key'   => 'max_attempts',
                'value' => '3',
            ],
            [
                'key'   => 'active_threshold',
                'value' => '5',
            ],
            [
                'key'   => 'support_email',
                'value' => 'soporte@tuempresa.com',
            ],
            // CLAVE PARA EL MODO MANTENIMIENTO
            [
                'key'   => 'site_offline',
                'value' => '0', // 0 = Desactivado, 1 = Activado
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']], 
                ['value' => $setting['value']]
            );
        }
    }
}