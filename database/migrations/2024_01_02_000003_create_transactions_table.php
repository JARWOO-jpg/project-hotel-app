<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['payment', 'dp_payment', 'refund', 'service_charge', 'checkout_payment']);
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'transfer', 'credit_card', 'e_wallet'])->default('transfer');
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
