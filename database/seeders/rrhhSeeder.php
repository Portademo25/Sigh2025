<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class rrhhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $analista = User::create([
            'cedula' => '000000000',
            'name'     => 'Analista',
            'apellido' => 'RRHH',
            'email'    => 'Analista@fona.gob.ve', // Ajustar al dominio real
            'password' => Hash::make('rrhh2026*'), // Contraseña segura
            'estatus_id' => 5, // Asumiendo que 1 es el ID del estatus "Activo"
            'rol_id' => 3, //Asumiendo que 3 es el ID del rol de Analista de RRHH
            'organizacion_id' => 9 // Asumiendo que 1 es el ID de la organización principal
        ]);

        // Asignar el rol (Usando Spatie)
        $analista->assignRole('analista_rrhh');

        $this->command->info('Usuario Analista creado exitosamente.');
    }
}
