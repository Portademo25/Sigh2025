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
        Schema::create('constancia', function (Blueprint $table) {
            $table->id();
            $table->integer('cedula');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('cargo');
            $table->integer('sueldo');
            $table->integer('bonoalimentacion');
            $table->date('fechaingreso');
            $table->date('fgeneracion');
            $table->string('codigo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('constancia');
    }
};
