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
        $table->string('codper', 10)->primary(); // Cédula/Código
        $table->string('nomper', 60);
        $table->string('apeper', 60);
        $table->date('fecingper');
        $table->string('coreleper', 100)->nullable(); // Correo
        $table->timestamps();
    });

    // Tabla de Nóminas (Espejo de sno_nomina)
    Schema::create('sno_nomina', function (Blueprint $table) {
        $table->string('codnom', 4)->primary();
        $table->string('desnom', 100);
        $table->timestamps();
    });

    // Tabla de Histórico de Periodos (sno_hperiodo)
    Schema::create('sno_hperiodo', function (Blueprint $table) {
        $table->id();
        $table->string('codnom', 4);
        $table->string('codper', 10);
        $table->string('codperi', 3);
        $table->decimal('montotper', 15, 2); // Monto total
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sigesp_sync_tables');
    }
};
