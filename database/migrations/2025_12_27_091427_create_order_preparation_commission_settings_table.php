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
        Schema::create('order_preparation_commission_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_order_amount', 15, 2)->default(0); // الحد الأدنى للطلب
            $table->decimal('max_order_amount', 15, 2)->nullable(); // الحد الأقصى (null = لا يوجد حد)
            $table->enum('commission_type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('commission_value', 15, 2); // المبلغ أو النسبة
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // الأولوية (الأعلى أولاً)
            $table->timestamps();
            
            // Indexes
            $table->index('is_active');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_preparation_commission_settings');
    }
};
