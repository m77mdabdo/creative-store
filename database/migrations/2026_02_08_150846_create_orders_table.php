<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shipping_address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();

            $table->string('guest_email')->nullable();
            $table->string('guest_phone', 30)->nullable();

            $table->char('currency', 3)->default('SAR');
            $table->decimal('exchange_rate', 14, 6)->default(1.000000);

            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('total', 12, 2);

            $table->enum('status', ['pending','confirmed','processing','shipped','delivered','cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid','paid','failed','refunded','partially_refunded'])->default('unpaid');

            $table->string('payment_method')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable();

            $table->timestamps();

            $table->index(['created_at','payment_status','status']);
            $table->index(['user_id','created_at']);
            $table->index('guest_email');
            $table->index('guest_phone');
        });
    }

    public function down(): void {
        Schema::dropIfExists('orders');
    }
};
