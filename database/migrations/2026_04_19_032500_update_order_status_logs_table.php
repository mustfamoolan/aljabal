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
        Schema::table('order_status_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('order_status_logs', 'waseet_status')) {
                $table->string('waseet_status')->nullable()->after('status');
            }
            
            if (Schema::hasColumn('order_status_logs', 'note')) {
                $table->renameColumn('note', 'notes');
            } elseif (!Schema::hasColumn('order_status_logs', 'notes')) {
                $table->text('notes')->nullable()->after('waseet_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_status_logs', function (Blueprint $table) {
            if (Schema::hasColumn('order_status_logs', 'waseet_status')) {
                $table->dropColumn('waseet_status');
            }
            
            if (Schema::hasColumn('order_status_logs', 'notes')) {
                $table->renameColumn('notes', 'note');
            }
        });
    }
};
