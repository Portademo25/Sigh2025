<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Roles
        $adminRole = Role::create(['name' => 'Administrador']);
        $talentoRole = Role::create(['name' => 'Talento Humano']);
        $empleadoRole = Role::create(['name' => 'Empleado']);


        //Permissions
        $permissionIndexCategories = Permission::create(['name' => 'view categories']);
        $permissionCreateCategories = Permission::create(['name' => 'create categories']);
        $permissionEditCategories = Permission::create(['name' => 'edit categories']);
        $permissionDeleteCategories = Permission::create(['name' => 'delete categories']);
        //productos
        $permissionIndexProducts = Permission::create(['name' => 'view products']);
        $permissionCreateProducts = Permission::create(['name' => 'create products']);
        $permissionEditProducts = Permission::create(['name' => 'edit products']);
        $permissionDeleteProducts = Permission::create(['name' => 'delete products']);
        //roles
        $permissionIndexRoles = Permission::create(['name' => 'view roles']);
        $permissionCreateRoles = Permission::create(['name' => 'create roles']);
        $permissionEditRoles = Permission::create(['name' => 'edit roles']);
        $permissionDeleteRoles = Permission::create(['name' => 'delete roles']);
        //usuarios
        $permissionIndexUsers = Permission::create(['name' => 'view users']);
        $permissionCreateUsers = Permission::create(['name' => 'create users']);
        $permissionEditUsers = Permission::create(['name' => 'edit users']);
        $permissionDeleteUsers = Permission::create(['name' => 'delete users']);
        //empleados
        $permissionIndexEmployees = Permission::create(['name' => 'view employees']);
        $permissionCreateEmployees = Permission::create(['name' => 'create employees']);
        $permissionEditEmployees = Permission::create(['name' => 'edit employees']);
        $permissionDeleteEmployees = Permission::create(['name' => 'delete employees']);
        $permissionAssignRoles = Permission::create(['name' => 'assign roles']);
        $permissionAssignPermissions = Permission::create(['name' => 'assign permissions']);


        //Asignar permisos a los roles
        $adminRole->givePermissionTo(Permission::all());

        //Permisos para el rol de Talento Humano
        $talentoRole->givePermissionTo([
            $permissionCreateEmployees,
            $permissionIndexEmployees,
            $permissionEditEmployees,
            $permissionDeleteEmployees,
            $permissionIndexCategories,
            $permissionIndexProducts,
            $permissionCreateCategories,
            $permissionCreateProducts,
            $permissionEditCategories,
            $permissionEditProducts,
            $permissionDeleteCategories,
            $permissionDeleteProducts,
        ]);

        //Permisos para el rol de Empleado
        $empleadoRole->givePermissionTo([
            $permissionIndexUsers,
            $permissionEditUsers,
        ]);

    }
}
