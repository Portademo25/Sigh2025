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
            'cedula' => '18788503', // Coloca la cédula del administrador aquí
            'name' => 'Luis Daniel',
            'apellido' => 'Martinez Gonzalez', // Coloca el apellido del administrador aquí
            'email' => 'Fona@fona.gob.ve', // Coloca el correo electrónico del administrador aquí
            'password' => Hash::make('Fona2025*'), // Coloca la contraseña del administrador aquí
            'estatus_id' => 5,
            'rol_id' => 1,
            'organizacion_id' => 9,
        ]);

        $admin->assignRole('admin');
    }
}
