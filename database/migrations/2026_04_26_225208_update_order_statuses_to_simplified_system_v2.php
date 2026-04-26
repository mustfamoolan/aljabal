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
        // 1. Migrate Dispatched/Completed orders to SENT_TO_GATEWAY
        DB::table('orders')
            ->whereIn('status', ['prepared', 'picked_up', 'completed', 'sent_to_gateway'])
            ->update(['status' => 'sent_to_gateway']);

        // 2. Migrate Internal Returns/Cancellations to NEW (so they can be re-evaluated)
        DB::table('orders')
            ->whereIn('status', ['cancelled', 'returned', 'replaced'])
            ->update(['status' => 'new']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
