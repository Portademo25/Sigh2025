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
        Schema::create('constancias_generadas', function (Blueprint $table) {
            $table->id();
        $table->string('token')->unique(); // El código del QR
        $table->string('cedula');
        $table->string('nombre_completo');
        $table->decimal('sueldo_integral', 15, 2);
        $table->decimal('monto_alimentacion', 15, 2);
        $table->string('cargo');
        $table->timestamp('fecha_generacion');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('constancias_generadas');
    }
};
