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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('qris_reference_id')->nullable()->after('payment_status')->index();
            $table->text('qris_qr_string')->nullable()->after('qris_reference_id');
            $table->timestamp('qris_expires_at')->nullable()->after('qris_qr_string');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['qris_reference_id', 'qris_qr_string', 'qris_expires_at']);
        });
    }
};
