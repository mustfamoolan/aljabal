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
        Schema::table('notifications', function (Blueprint $table) {
            $table->nullableMorphs('notifiable');
            $table->foreignId('user_id')->nullable()->change();
        });

        // Migrate existing user_id data to polymorphic columns
        DB::table('notifications')->whereNotNull('user_id')->update([
            'notifiable_id' => DB::raw('user_id'),
            'notifiable_type' => 'App\\Models\\User',
        ]);
        
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['notifiable_id', 'notifiable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropMorphs('notifiable');
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
