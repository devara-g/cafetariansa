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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name', 100);
            $table->string('customer_phone', 25);
            $table->enum('customer_type', ['student', 'teacher', 'staff', 'guest'])->default('student');
            $table->enum('pickup_type', ['pickup_at_stand', 'deliver_to_room'])->default('pickup_at_stand');
            $table->string('delivery_location', 100)->nullable();
            $table->string('pickup_time_slot', 60)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('service_fee')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('total_amount');
            $table->enum('order_status', [
                'pending_payment',
                'paid',
                'confirmed',
                'preparing',
                'ready_for_pickup',
                'completed',
                'cancelled',
            ])->default('pending_payment');
            $table->string('cancelled_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('order_number');
            $table->index('order_status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
