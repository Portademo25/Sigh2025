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
    Schema::table('users', function (Blueprint $table) {
        // Verificamos si NO existe antes de intentar crearla
        if (!Schema::hasColumn('users', 'cedula')) {
            $table->string('cedula', 15)->nullable();
        }

        if (!Schema::hasColumn('users', 'codper')) {
            $table->string('codper', 10)->nullable()->unique();
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
