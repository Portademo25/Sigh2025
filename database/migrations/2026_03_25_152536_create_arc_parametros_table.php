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
    Schema::create('arc_parametros', function (Blueprint $table) {
        $table->id();
        $table->integer('anio')->unique(); // Un registro único por año fiscal
        $table->json('nominas');          // Guardaremos el array de códigos de nómina
        $table->json('conceptos');        // Guardaremos el array de códigos de conceptos
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arc_parametros');
    }
};
