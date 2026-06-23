<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Midtrans order_id (snap transaction reference)
            $table->string('midtrans_order_id')->nullable()->after('transaction_code');
            // Snap token dari Midtrans untuk redirect ke payment page
            $table->string('snap_token', 512)->nullable()->after('midtrans_order_id');
            // URL redirect Snap
            $table->string('snap_redirect_url', 512)->nullable()->after('snap_token');
            // Payment method detail dari Midtrans (gopay, bca, mandiri, dll)
            $table->string('payment_channel', 100)->nullable()->after('payment_method');
            // VA number atau payment code
            $table->string('va_number', 50)->nullable()->after('payment_channel');
            // Fraud status dari Midtrans (accept, challenge, deny)
            $table->string('fraud_status', 20)->nullable()->after('va_number');
            // Raw response dari Midtrans notification
            $table->json('midtrans_response')->nullable()->after('fraud_status');
            // Waktu pembayaran berhasil dari Midtrans
            $table->timestamp('paid_at')->nullable()->after('midtrans_response');
        });

        // Tambah 'midtrans' ke enum payment_method - SQLite & MySQL compat
        // Untuk MySQL: perlu modify enum
        // Untuk SQLite: tidak bisa modify enum, kolom sudah ada
        try {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('payment_method', 50)->nullable()->change();
            });
        } catch (\Exception $e) {
            // Kolom sudah ada atau DB tidak support
        }
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'midtrans_order_id', 'snap_token', 'snap_redirect_url',
                'payment_channel', 'va_number', 'fraud_status',
                'midtrans_response', 'paid_at',
            ]);
        });
    }
};
