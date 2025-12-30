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
        Schema::create('gift_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الهدية/البوكس
            $table->enum('type', ['gift', 'gift_box']); // نوع الهدية
            $table->decimal('price', 15, 2)->nullable(); // سعر الهدية (لنوع gift)
            $table->integer('min_books')->nullable(); // الحد الأدنى لعدد الكتب (لنوع gift_box)
            $table->integer('max_books')->nullable(); // الحد الأقصى لعدد الكتب (لنوع gift_box)
            $table->decimal('box_price', 15, 2)->nullable(); // سعر البوكس لهذا العدد (لنوع gift_box)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_settings');
    }
};
