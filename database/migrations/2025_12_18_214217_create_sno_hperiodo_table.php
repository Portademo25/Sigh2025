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
        Schema::create('sno_hperiodo', function (Blueprint $table) {
    $table->id(); // ID autoincremental local
    $table->string('codemp');
    $table->string('codnom');
    $table->string('codperi');
    $table->date('fecdesper')->nullable();
    $table->date('fechasper')->nullable();
    $table->char('cerperi', 1)->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sno_hperiodo');
    }
};
