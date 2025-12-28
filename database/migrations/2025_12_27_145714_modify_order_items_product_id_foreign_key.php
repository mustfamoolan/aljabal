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
        Schema::table('order_items', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['product_id']);
        });

        // Make product_id nullable
        DB::statement('ALTER TABLE `order_items` MODIFY `product_id` BIGINT UNSIGNED NULL');

        // Add new foreign key with SET NULL on delete
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['product_id']);
        });

        // Make product_id not nullable again
        DB::statement('ALTER TABLE `order_items` MODIFY `product_id` BIGINT UNSIGNED NOT NULL');

        // Restore original foreign key with CASCADE
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }
};
