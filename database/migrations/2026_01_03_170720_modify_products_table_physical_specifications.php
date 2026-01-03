<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Remove weight and color fields
            $table->dropColumn(['weight', 'color']);

            // Modify size from string to enum
            $table->dropColumn('size');
            $table->enum('size', ['large', 'waziri', 'ruqai', 'kaffi', 'pocket'])->nullable()->after('weight_value')->comment('الحجم');

            // Convert product_type enum to is_original boolean
            $table->dropColumn('product_type');
            $table->boolean('is_original')->default(false)->after('sku')->comment('أصلي أم عادي');

            // Add is_hardcover field
            $table->boolean('is_hardcover')->default(false)->after('page_count')->comment('هاردكفر أم ورقي');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Restore weight and color
            $table->decimal('weight', 8, 2)->nullable()->after('weight_value')->comment('الوزن بالكيلوغرام');
            $table->string('color')->nullable()->after('long_description');

            // Restore size as string
            $table->dropColumn('size');
            $table->string('size')->nullable()->after('weight')->comment('الحجم (مثال: 20x15x2 سم)');

            // Restore product_type enum
            $table->dropColumn('is_original');
            $table->enum('product_type', ['original', 'normal'])->after('sku');

            // Remove is_hardcover
            $table->dropColumn('is_hardcover');
        });
    }
};
