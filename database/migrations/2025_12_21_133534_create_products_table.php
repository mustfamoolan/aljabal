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
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique()->nullable();
            $table->enum('product_type', ['original', 'normal']);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('author')->nullable();
            $table->string('publisher')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->decimal('retail_price', 10, 2)->nullable();
            $table->decimal('wholesale_price', 10, 2)->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('min_quantity')->nullable();
            $table->integer('available_quantity')->default(0);
            $table->enum('unit_type', ['weight', 'carton', 'set'])->nullable();
            $table->enum('weight_unit', ['kilogram', 'gram'])->nullable();
            $table->decimal('weight_value', 8, 2)->nullable();
            $table->integer('carton_quantity')->nullable();
            $table->integer('set_quantity')->nullable();
            $table->string('shelf')->nullable();
            $table->string('compartment')->nullable();
            $table->text('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->string('color')->nullable();
            $table->date('first_purchase_date')->nullable();
            $table->date('last_purchase_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
