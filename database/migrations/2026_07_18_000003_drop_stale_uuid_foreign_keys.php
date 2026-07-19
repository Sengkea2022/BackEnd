<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop all stale FK constraints that still reference *_uuid columns.
     * After the column rename, these constraints now reference non-existent
     * columns (e.g. stores.user_code → users.uuid, but uuid is not the PK
     * we use for code-based relationships).
     *
     * Referential integrity is now enforced at the application layer using
     * the standardized `code` business key.
     */
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropForeign('stores_user_uuid_foreign');
        });

        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->dropForeign('exchange_rates_from_currency_uuid_foreign');
            $table->dropForeign('exchange_rates_to_currency_uuid_foreign');
        });

        Schema::table('guest_links', function (Blueprint $table) {
            $table->dropForeign('guest_links_store_uuid_foreign');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign('notifications_order_uuid_foreign');
            $table->dropForeign('notifications_user_uuid_foreign');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign('order_items_order_uuid_foreign');
            $table->dropForeign('order_items_price_uuid_foreign');
            $table->dropForeign('order_items_product_uuid_foreign');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_currency_uuid_foreign');
            $table->dropForeign('orders_customer_uuid_foreign');
            $table->dropForeign('orders_guest_link_uuid_foreign');
            $table->dropForeign('orders_store_uuid_foreign');
        });

        Schema::table('prices', function (Blueprint $table) {
            $table->dropForeign('prices_currency_uuid_foreign');
            $table->dropForeign('prices_product_uuid_foreign');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_category_uuid_foreign');
            $table->dropForeign('products_store_uuid_foreign');
        });

        Schema::table('profits', function (Blueprint $table) {
            $table->dropForeign('profits_order_uuid_foreign');
            $table->dropForeign('profits_store_uuid_foreign');
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropForeign('stocks_product_uuid_foreign');
            $table->dropForeign('stocks_store_uuid_foreign');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign('transactions_order_uuid_foreign');
        });
    }

    public function down(): void
    {
        // FK constraints are not restored — they referenced the old uuid columns
        // which are now renamed. Restoring would require re-creating the old schema.
    }
};
