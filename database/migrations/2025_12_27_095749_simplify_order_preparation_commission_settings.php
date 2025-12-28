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
        Schema::table('order_preparation_commission_settings', function (Blueprint $table) {
            $table->dropColumn(['min_order_amount', 'max_order_amount', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_preparation_commission_settings', function (Blueprint $table) {
            $table->decimal('min_order_amount', 15, 2)->default(0)->after('id');
            $table->decimal('max_order_amount', 15, 2)->nullable()->after('min_order_amount');
            $table->integer('priority')->default(0)->after('commission_value');
        });
    }
};
