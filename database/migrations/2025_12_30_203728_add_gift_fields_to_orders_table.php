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
            $table->foreignId('gift_id')->nullable()->after('delivery_fee')->constrained('gift_settings')->onDelete('set null');
            $table->foreignId('gift_box_id')->nullable()->after('gift_id')->constrained('gift_settings')->onDelete('set null');
            $table->decimal('gift_price', 15, 2)->default(0)->after('gift_box_id');
            
            $table->index('gift_id');
            $table->index('gift_box_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['gift_id']);
            $table->dropForeign(['gift_box_id']);
            $table->dropIndex(['gift_id']);
            $table->dropIndex(['gift_box_id']);
            $table->dropColumn(['gift_id', 'gift_box_id', 'gift_price']);
        });
    }
};
