<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // FK متأخر (product_images.variant_id)
        Schema::table('product_images', function (Blueprint $table) {
            $table->foreign('variant_id')->references('id')->on('product_variants')->nullOnDelete();
        });

        // CHECK constraints (MySQL 8+ / PostgreSQL)
        DB::statement("ALTER TABLE cart_items ADD CONSTRAINT chk_cart_items_quantity CHECK (quantity > 0)");
        DB::statement("ALTER TABLE cart_items ADD CONSTRAINT chk_cart_items_price CHECK (price_snapshot >= 0)");

        DB::statement("ALTER TABLE products ADD CONSTRAINT chk_products_price CHECK (base_price >= 0)");
        DB::statement("ALTER TABLE products ADD CONSTRAINT chk_products_tax CHECK (tax_rate >= 0)");

        DB::statement("ALTER TABLE product_variants ADD CONSTRAINT chk_variants_stock CHECK (stock >= 0)");
        DB::statement("ALTER TABLE product_variants ADD CONSTRAINT chk_variants_price CHECK (price IS NULL OR price >= 0)");

        DB::statement("ALTER TABLE order_items ADD CONSTRAINT chk_order_items_qty CHECK (quantity > 0)");
        DB::statement("ALTER TABLE order_items ADD CONSTRAINT chk_order_items_price CHECK (unit_price >= 0)");
        DB::statement("ALTER TABLE order_items ADD CONSTRAINT chk_order_items_tax CHECK (tax_amount >= 0)");
        DB::statement("ALTER TABLE order_items ADD CONSTRAINT chk_order_items_line_total CHECK (line_total >= 0)");

        DB::statement("ALTER TABLE reviews ADD CONSTRAINT chk_reviews_rating CHECK (rating BETWEEN 1 AND 5)");

        DB::statement("ALTER TABLE orders ADD CONSTRAINT chk_orders_exchange_rate CHECK (exchange_rate > 0)");
    }

    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropForeign(['variant_id']);
        });

        // ملاحظة: DROP CHECK يختلف حسب DB
        // MySQL: ALTER TABLE table_name DROP CHECK constraint_name;
        // PostgreSQL: ALTER TABLE table_name DROP CONSTRAINT constraint_name;
    }
};
