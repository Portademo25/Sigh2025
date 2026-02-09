<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserEmpleadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      

    $employee2 = User::create([
            'cedula' => '6484870', // Coloca la cédula del administrador aquí
            'name' => 'Maria Elena',
            'apellido' => 'Castro', // Coloca el apellido del administrador aquí
            'email' => 'mcastro@fona.gob.ve', // Coloca el correo electrónico del administrador aquí
            'password' => Hash::make('Fona2025*'), // Coloca la contraseña del administrador aquí
            'estatus_id' => 5,
            'rol_id' => 1,
            'organizacion_id' => 9
]);
    $employee2->assignRole('empleado');
    }
}
