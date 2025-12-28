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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->unique()->after('name');
            $table->enum('type', ['admin', 'employee'])->default('employee')->after('phone');
            $table->boolean('is_active')->default(true)->after('type');
        });

        // Make email nullable separately to avoid issues with Doctrine DBAL
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'type', 'is_active']);
            $table->string('email')->nullable(false)->change();
        });
    }
};
