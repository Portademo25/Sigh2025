<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleAnalista = Role::create(['name' => 'analista_rrhh']);

    // 2. Definir Permisos Específicos para el Analista
    $permisosAnalista = [
        'ver_dashboard',
        'ver_personal',
        'editar_personal',
        'generar_reportes',
        'descargar_arc_admin', // El que acabamos de crear
    ];

    // 3. Asignar Permisos al Rol
    foreach ($permisosAnalista as $permiso) {
        Permission::findOrCreate($permiso);
        $roleAnalista->givePermissionTo($permiso);
    }
    }
}
