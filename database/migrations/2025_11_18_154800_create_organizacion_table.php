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
        Schema::create('organizacion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_sigesp');
            $table->string('nombre_organizacion');
            $table->string('abreviacion_organizacion');
            $table->integer('estatus_constacia');
            $table->integer('estatus_id');
            $table->string('base_datos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizacion');
    }
};
