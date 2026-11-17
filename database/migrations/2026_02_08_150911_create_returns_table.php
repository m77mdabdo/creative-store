<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('order_id')
                ->constrained('orders')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Return status
            $table->enum('status', [
                'requested',
                'approved',
                'rejected',
                'received',
                'refunded',
                'partial_refunded'
            ])->default('requested');

            // Details
            $table->text('reason')->nullable();

            $table->boolean('refund_shipping')->default(false);
            $table->boolean('refund_tax')->default(true);

            $table->decimal('shipping_refund_amount', 12, 2)->default(0);
            $table->decimal('tax_refund_amount', 12, 2)->default(0);

            // Dates
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('processed_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['order_id','status']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
