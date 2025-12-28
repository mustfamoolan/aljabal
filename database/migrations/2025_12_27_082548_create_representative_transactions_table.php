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
        Schema::create('representative_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('representative_id')->constrained('representatives')->onDelete('cascade');
            $table->enum('type', ['deposit', 'withdrawal', 'commission', 'bonus', 'deduction', 'refund']);
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('completed');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('withdrawal_request_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('balance_before', 15, 2)->default(0.00);
            $table->decimal('balance_after', 15, 2)->default(0.00);
            $table->timestamps();

            $table->index('representative_id');
            $table->index('type');
            $table->index('status');
            $table->index('created_at');
            $table->index('withdrawal_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('representative_transactions');
    }
};
