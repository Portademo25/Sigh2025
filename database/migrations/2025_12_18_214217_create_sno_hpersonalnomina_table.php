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
        Schema::create('sno_hpersonalnomina', function (Blueprint $table) {
    $table->id();
    $table->string('codemp');
    $table->string('codnom');
    $table->string('codper');
    $table->string('codque')->nullable();
    $table->string('codasicar')->nullable();
    $table->string('tabpersonal')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sno_hpersonalnomina');
    }
};
