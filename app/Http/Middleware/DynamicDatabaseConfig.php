<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class DynamicDatabaseConfig
{
    public function handle(Request $request, Closure $next)
    {
        // Solo procedemos si la tabla settings existe
        if (Schema::hasTable('settings')) {
            $settings = DB::table('settings')->pluck('value', 'key');

            // 1. Configurar Conexión LOCAL (pgsql)
            if (isset($settings['db_local_host'])) {
                Config::set('database.connections.pgsql.host', $settings['db_local_host']);
                Config::set('database.connections.pgsql.database', $settings['db_local_name']);
                Config::set('database.connections.pgsql.username', $settings['db_local_user']);

                if (!empty($settings['db_local_pass'])) {
                    Config::set('database.connections.pgsql.password', decrypt($settings['db_local_pass']));
                }
            }

            // 2. Configurar Conexión SIGESP (Externa)
            if (isset($settings['db_sigesp_host'])) {
                Config::set('database.connections.sigesp', [
                    'driver'   => 'pgsql', // O el driver que use tu SIGESP (oracle, mysql, etc)
                    'host'     => $settings['db_sigesp_host'],
                    'port'     => $settings['db_sigesp_port'] ?? '5432',
                    'database' => $settings['db_sigesp_name'],
                    'username' => $settings['db_sigesp_user'],
                    'password' => !empty($settings['db_sigesp_pass']) ? decrypt($settings['db_sigesp_pass']) : '',
                    'charset'  => 'utf8',
                    'prefix'   => '',
                    'schema'   => 'public',
                ]);
            }

            // Refrescar la conexión para que tome los nuevos valores
            DB::purge('pgsql');
            DB::purge('sigesp');
        }

        return $next($request);
    }
}
