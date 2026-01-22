<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SyncSigespEmployees extends Command
{
    protected $signature = 'sigesp:sync-employees';
    protected $description = 'Sincroniza los trabajadores desde la base de datos SIGESP';

    public function handle()
    {
        $this->info('Iniciando sincronización con SIGESP...');

        // 1. Obtener configuración de la tabla settings
        $settings = DB::table('settings')->pluck('value', 'key');

        // 2. Configurar la conexión SIGESP dinámicamente
        config(['database.connections.sigesp_sync' => [
            'driver'   => 'oracle', // O el driver que configuraste
            'host'     => $settings['db_sigesp_host'],
            'port'     => $settings['db_sigesp_port'],
            'database' => $settings['db_sigesp_name'],
            'username' => $settings['db_sigesp_user'],
            'password' => decrypt($settings['db_sigesp_pass']),
            'charset'  => 'utf8',
        ]]);

        try {
            // 3. Consultar trabajadores en SIGESP (Ejemplo de query básica)
            // Ajusta los nombres de tabla y campos según tu versión de SIGESP
            $trabajadores = DB::connection('sigesp_sync')
                ->table('sno_personal')
                ->select('cedper as cedula', 'nomper as nombre', 'apeper as apellido', 'corele as email')
                ->get();

            foreach ($trabajadores as $t) {
                // 4. Actualizar o Crear en la base de datos local
                User::updateOrCreate(
                    ['cedula' => $t->cedula],
                    [
                        'name'     => $t->nombre . ' ' . $t->apellido,
                        'email'    => $t->email ?? ($t->cedula . '@institucion.gob.ve'),
                        'password' => Hash::make($t->cedula), // Password inicial por defecto
                        // Otros campos como cargo, departamento, etc.
                    ]
                );
            }

            // Guardar fecha de última sincronización en settings
            DB::table('settings')->updateOrInsert(
                ['key' => 'last_sync_date'],
                ['value' => now()->format('Y-m-d H:i:s')]
            );

            $this->info('Sincronización completada con éxito.');

        } catch (\Exception $e) {
            $this->error('Error sincronizando: ' . $e->getMessage());
        }
    }
}
