<?php
// database/migrations/[timestamp]_add_login_attempts_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('login_attempts')->default(0)->after('fecha_contratacion');
            $table->timestamp('locked_until')->nullable()->after('login_attempts');
            $table->boolean('is_locked')->default(false)->after('locked_until');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['login_attempts', 'locked_until', 'is_locked']);
        });
    }
};
