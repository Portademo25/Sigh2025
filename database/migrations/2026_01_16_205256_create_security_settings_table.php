<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('security_settings', function (Blueprint $table) {
        $table->id();
        $table->string('key')->unique(); // Ejemplo: 'max_attempts'
        $table->string('value');          // Ejemplo: '5'
        $table->string('description')->nullable();
        $table->timestamps();
    });

    // Insertar valores por defecto para que la vista no esté vacía
    DB::table('security_settings')->insert([
        ['key' => 'max_attempts', 'value' => '3', 'description' => 'Intentos fallidos permitidos'],
        ['key' => 'lockout_time', 'value' => '15', 'description' => 'Minutos de bloqueo'],
        ['key' => 'session_lifetime', 'value' => '120', 'description' => 'Duración de sesión'],
        ['key' => 'require_special_chars', 'value' => '0', 'description' => 'Exigir caracteres especiales'],
    ]);
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_settings');
    }
};
