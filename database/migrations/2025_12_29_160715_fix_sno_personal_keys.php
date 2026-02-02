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
    Schema::table('sno_personal', function (Blueprint $table) {
        // 1. Solo agregamos el índice compuesto si NO existe
        // Usamos un nombre personalizado para evitar conflictos: 'sync_unique'
        $table->unique(['codemp', 'codper'], 'sno_personal_sync_unique');

        // 2. Para la cédula, como el error dice que ya existe,
        // Laravel fallará si intentas crearla de nuevo.
        // Solo la agregamos si no está en la tabla.
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sno_personal', function (Blueprint $table) {
            //
        });
    }
};
