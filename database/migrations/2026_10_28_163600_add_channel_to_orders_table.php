<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'channel')) {
                $table->string('channel', 30)->default('pos')->after('customer_id')->index();
            }
        });

        // Populate channel for existing orders based on notes
        DB::table('orders')
            ->where('notes', 'like', '%"type":"ppob_%')
            ->orWhere('notes', 'like', '%"type":"ppob%')
            ->update(['channel' => 'ppob']);

        DB::table('orders')
            ->where('notes', 'like', '%"type":"wallet_topup"%')
            ->update(['channel' => 'wallet_topup']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'channel')) {
                $table->dropColumn('channel');
            }
        });
    }
};
