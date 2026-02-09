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
        Schema::create('arcs_generadas', function (Blueprint $table) {

        $table->id();
        $table->string('token')->unique();
        $table->string('cedula');
        $table->string('nombre_completo');
        $table->integer('ano_fiscal');
        $table->decimal('total_remuneracion', 15, 2);
        $table->decimal('total_retencion', 15, 2);
        $table->timestamp('fecha_generacion');
        $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arcs_generadas');
    }
};
