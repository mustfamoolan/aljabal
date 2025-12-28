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
        // Remove percentage type and set all to fixed
        DB::table('order_preparation_commission_settings')
            ->where('commission_type', 'percentage')
            ->update(['commission_type' => 'fixed']);

        // Modify the column to only allow 'fixed'
        Schema::table('order_preparation_commission_settings', function (Blueprint $table) {
            $table->dropColumn('commission_type');
        });

        Schema::table('order_preparation_commission_settings', function (Blueprint $table) {
            $table->decimal('commission_value', 15, 2)->default(0)->change(); // Allow zero
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_preparation_commission_settings', function (Blueprint $table) {
            $table->enum('commission_type', ['fixed', 'percentage'])->default('fixed')->after('max_order_amount');
        });
    }
};

