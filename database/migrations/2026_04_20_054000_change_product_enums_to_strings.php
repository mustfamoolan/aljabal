<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Change unit_type from enum to string for flexibility
            $table->string('unit_type', 50)->nullable()->change();
            
            // Change size from enum to string for flexibility
            $table->string('size', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Revert to enum if needed (Note: this might fail if new values exist)
            $table->enum('unit_type', ['weight', 'carton', 'set', 'piece'])->nullable()->change();
            $table->enum('size', ['large', 'waziri', 'ruqai', 'kaffi', 'pocket'])->nullable()->change();
        });
    }
};
