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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('waseet_order_id')->nullable()->after('is_withdrawal_order');
            $table->text('waseet_tracking_url')->nullable()->after('waseet_order_id');
            $table->string('waseet_status')->nullable()->after('waseet_tracking_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['waseet_order_id', 'waseet_tracking_url', 'waseet_status']);
        });
    }
};
