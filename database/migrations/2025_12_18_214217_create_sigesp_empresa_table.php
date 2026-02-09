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
        Schema::create('sigesp_empresa', function (Blueprint $table) {
         $table->string('codemp')->primary();
             $table->string('nombre');
             $table->string('rif')->nullable();
             $table->text('dirlibemp')->nullable();
             $table->string('telemp')->nullable();
             $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sigesp_empresa');
    }
};
