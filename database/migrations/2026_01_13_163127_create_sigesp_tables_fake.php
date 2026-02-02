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
       Schema::create('sno_hresumen', function (Blueprint $table) {
            $table->string('codnom', 4);
            $table->string('codperi', 10);
            $table->string('codper', 10);
            $table->decimal('monpago', 15, 2);
            $table->primary(['codnom', 'codperi', 'codper']);
        });

        Schema::create('sno_hcalcurp', function (Blueprint $table) {
            $table->string('codnom', 4);
            $table->string('codperi', 10);
            $table->string('codper', 10);
            $table->string('codcon', 10);
            $table->decimal('valcalcur', 15, 2);
            $table->char('tipcon', 1); // P = Asignación, D = Deducción
        });

        Schema::create('sno_concepto', function (Blueprint $table) {
            $table->string('codcon', 10)->primary();
            $table->string('nomcon', 100);
            $table->string('titcon', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sno_hcalcurp');
        Schema::dropIfExists('sno_hresumen');
        Schema::dropIfExists('sno_concepto');
    }
};
