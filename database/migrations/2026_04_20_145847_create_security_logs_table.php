<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('security_logs', function (Blueprint $table) {
        $table->id();
        
        // El tipo de evento (ej: 'Login Fallido', 'Cambio de Clave')
        $table->string('event'); 
        
        // Quién lo hizo (Cédula o ID del usuario). 
        // Lo dejamos nullable por si es un invitado que falla el login.
        $table->string('user_identifier')->nullable(); 
        
        // Datos de red
        $table->ipAddress('ip_address'); // Formato IP optimizado
        $table->text('user_agent')->nullable(); // Navegador/Sistema Operativo
        
        // Clasificación: 'Baja', 'Media', 'Alta', 'Crítica'
        $table->string('severity')->default('Baja'); 
        
        // Detalles extra en formato JSON (ej: qué campos cambiaron)
        $table->json('details')->nullable(); 

        // timestamps() nos da automáticamente 'created_at' (fecha y hora del evento)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
