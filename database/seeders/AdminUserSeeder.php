<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'usuario' => 'Administrador',
            'nombre' => 'Luis Daniel',
            'apellido' => 'Martinez Gonzalez',
            'cedula' => '18788503',
            'email' => 'Fona@fona.gob.ve',
            'organizacion_id' => 9,
            'estatus_id' => 1,
            'rol_id' => 1,
            'password' => Hash::make('Fona2025*'), // Cambia 'password' por la contraseña que desees
        ]);

        $admin->assignRole('Administrador');
    }
}
