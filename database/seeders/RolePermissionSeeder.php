<?php
// database/seeders/RolePermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RolePermissionSeeder extends Seeder
{
    public function run()
    {
       //roles
       $adminRole = Role::create(['name' => 'admin']);
       $empleadoRole = Role::create(['name' => 'empleado']);


         //permisos para  usuarios
         $permissionIndexUsuarios =  Permission::create(['name' => 'ver usuarios']);
         $permissionCreateUsuarios =  Permission::create(['name' => 'crear usuarios']);
         $permissionEditUsuarios =  Permission::create(['name' => 'editar usuarios']);
         $permissionDeleteUsuarios =  Permission::create(['name' => 'eliminar usuarios']);
         $permissionAssignRoles =  Permission::create(['name' => 'asignar roles']);


         //permisos para roles
            $permissionIndexRoles =  Permission::create(['name' => 'ver roles']);
            $permissionCreateRoles =  Permission::create(['name' => 'crear roles']);
            $permissionEditRoles =  Permission::create(['name' => 'editar roles']);
            $permissionDeleteRoles =  Permission::create(['name' => 'eliminar roles']);

        //permisos para permisos
            $permissionIndexPermisos =  Permission::create(['name' => 'ver permisos']);
            $permissionCreatePermisos =  Permission::create(['name' => 'crear permisos']);
            $permissionEditPermisos =  Permission::create(['name' => 'editar permisos']);
            $permissionDeletePermisos =  Permission::create(['name' => 'eliminar permisos']);
            $permissionAssingnPermisos =  Permission::create(['name' => 'asignar permisos']);

        //permisos para documentos
            $permissionIndexDocumentos =  Permission::create(['name' => 'ver documentos']);
            $permissionCreateDocumentos =  Permission::create(['name' => 'crear documentos']);
            $permissionEditDocumentos =  Permission::create(['name' => 'editar documentos']);
            $permissionDeleteDocumentos =  Permission::create(['name' => 'eliminar documentos']);
            $permissionDownloadDocumentos =  Permission::create(['name' => 'descargar documentos']);



            //asignar permisos al rol admin
            $adminRole->givePermissionTo(Permission::all());

            //asignar permisos al rol empleado
            $empleadoRole->givePermissionTo([
                $permissionIndexDocumentos,
                $permissionCreateDocumentos,
                $permissionEditDocumentos,
                $permissionDeleteDocumentos,
                $permissionDownloadDocumentos,
            ]);


    }
}
