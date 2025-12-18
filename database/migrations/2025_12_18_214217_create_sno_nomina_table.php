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
       Schema::create('sno_nomina', function (Blueprint $table) {
    $table->string('codemp');
    $table->string('codnom')->primary(); // Ojo: en SIGESP la PK suele ser codemp + codnom
    $table->string('desnom');
    $table->string('tipnom')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sno_nomina');
    }
};
