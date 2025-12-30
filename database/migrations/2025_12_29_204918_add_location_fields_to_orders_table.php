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
            $table->foreignId('governorate_id')->nullable()->after('customer_address')->constrained('governorates')->onDelete('set null');
            $table->foreignId('district_id')->nullable()->after('governorate_id')->constrained('districts')->onDelete('set null');
            $table->decimal('delivery_fee', 15, 2)->default(0)->after('district_id');
            
            $table->index('governorate_id');
            $table->index('district_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['governorate_id']);
            $table->dropForeign(['district_id']);
            $table->dropIndex(['governorate_id']);
            $table->dropIndex(['district_id']);
            $table->dropColumn(['governorate_id', 'district_id', 'delivery_fee']);
        });
    }
};
