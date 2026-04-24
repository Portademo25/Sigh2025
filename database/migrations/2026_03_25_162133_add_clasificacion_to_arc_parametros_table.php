<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arc_parametros', function (Blueprint $row) {
            // Usamos tipo JSON para guardar el mapa de asignaciones, islr, sso, etc.
            // Si tu MariaDB/MySQL es antiguo, puedes usar text()
            $row->json('clasificacion')->nullable()->after('conceptos');
        });
    }

    public function down(): void
    {
        Schema::table('arc_parametros', function (Blueprint $row) {
            $row->dropColumn('clasificacion');
        });
    }
};
