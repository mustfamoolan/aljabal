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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            // Quantity
            $table->integer('quantity');
            
            // Prices
            $table->decimal('wholesale_price', 15, 2); // سعر الجملة من قاعدة البيانات
            $table->decimal('customer_price', 15, 2); // سعر البيع للزبون الذي أدخله المندوب
            $table->decimal('profit_per_item', 15, 2); // الفرق بين customer_price و wholesale_price
            
            // Subtotals
            $table->decimal('subtotal', 15, 2); // quantity × customer_price
            $table->decimal('profit_subtotal', 15, 2); // quantity × profit_per_item
            
            $table->timestamps();
            
            // Indexes
            $table->index('order_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
