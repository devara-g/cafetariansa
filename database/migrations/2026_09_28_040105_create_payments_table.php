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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('payment_reference', 100)->unique();
            $table->string('gateway_transaction_id', 150)->nullable()->index();
            $table->string('gateway', 50)->default('midtrans'); // midtrans, xendit, tripay, duitku, cash
            $table->string('payment_method', 50)->nullable(); // qris, gopay, shopeepay, bca_va, bri_va, etc.
            $table->unsignedBigInteger('amount');
            $table->unsignedInteger('fee')->default(0);
            $table->enum('status', ['pending', 'paid', 'settled', 'expired', 'failed', 'refunded'])->default('pending');
            $table->string('snap_token')->nullable(); // Midtrans Snap token
            $table->text('checkout_url')->nullable(); // Payment gateway redirect URL
            $table->text('qr_code_url')->nullable(); // Direct QR code image URL
            $table->text('qr_string')->nullable(); // Raw QRIS payload string
            $table->string('va_number', 50)->nullable(); // Virtual Account number
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('gateway_response')->nullable(); // Raw JSON response for auditing
            $table->timestamps();

            $table->index('status');
            $table->index(['gateway', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
