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
        Schema::dropIfExists('order_preparation_commission_exceptions');
        
        Schema::create('order_preparation_commission_exceptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('representative_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            
            $table->foreign('representative_id', 'opce_rep_fk')->references('id')->on('representatives')->onDelete('cascade');
            $table->foreign('user_id', 'opce_user_fk')->references('id')->on('users')->onDelete('cascade');
            $table->decimal('commission_value', 15, 2)->default(0); // المبلغ (يمكن أن يكون صفر)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('representative_id');
            $table->index('user_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_preparation_commission_exceptions');
    }
};
