<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sno_personal', function (Blueprint $table) {
    $table->id();
    $table->string('codemp');           // Código de empresa (necesario para la relación)
    $table->string('codper');           // Código interno del personal
    $table->string('cedper')->unique(); // Cédula (la usamos como llave única para sincronizar)
    $table->string('nomper');           // Nombres
    $table->string('apeper');           // Apellidos
    $table->string('coreleper')->nullable(); // Correo electrónico
    $table->date('fecingper')->nullable();   // Fecha de ingreso
    $table->string('codger')->nullable();    // Código de gerencia o unidad
    $table->timestamps();               // created_at y updated_at
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sno_personal');
    }
};
