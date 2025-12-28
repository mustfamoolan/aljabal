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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // Customer information
            $table->string('customer_name');
            $table->text('customer_address');
            $table->string('customer_phone');
            $table->string('customer_phone_2')->nullable();
            $table->string('customer_social_media')->nullable();
            $table->text('customer_notes')->nullable();
            
            // Order information
            $table->enum('status', ['new', 'prepared', 'completed', 'cancelled', 'returned', 'replaced'])->default('new');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('total_profit', 15, 2)->default(0);
            $table->decimal('preparation_commission', 15, 2)->default(0);
            $table->decimal('final_profit', 15, 2)->default(0); // total_profit - preparation_commission
            
            // Who created the order
            $table->foreignId('representative_id')->nullable()->constrained('representatives')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Dates
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('representative_id');
            $table->index('created_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
