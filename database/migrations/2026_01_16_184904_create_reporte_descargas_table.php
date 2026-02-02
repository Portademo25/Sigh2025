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
        Schema::create('reporte_descargas', function (Blueprint $table) {
            $table->id();
            $table->string('cedula');
    $table->string('nombre_empleado');
    $table->string('tipo_reporte'); // ARC, Recibo, IVSS
    $table->string('detalles')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporte_descargas');
    }
};
