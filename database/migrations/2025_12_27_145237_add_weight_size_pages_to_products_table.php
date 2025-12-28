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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('weight', 8, 2)->nullable()->after('weight_value')->comment('الوزن بالكيلوغرام');
            $table->string('size')->nullable()->after('weight')->comment('الحجم (مثال: 20x15x2 سم)');
            $table->integer('page_count')->nullable()->after('size')->comment('عدد الصفحات');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['weight', 'size', 'page_count']);
        });
    }
};
