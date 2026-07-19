<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Standardize all code and *_code columns to VARCHAR(50).
     * Previously, foreign keys renamed from *_uuid retained their CHAR(36) type,
     * causing type mismatches with the primary VARCHAR(50) code columns.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('code', 50)->change();
            $table->string('store_code', 50)->nullable()->change();
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->string('user_code', 50)->change();
        });

        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->string('from_currency_code', 50)->change();
            $table->string('to_currency_code', 50)->change();
        });

        Schema::table('guest_links', function (Blueprint $table) {
            $table->string('store_code', 50)->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('store_code', 50)->change();
            $table->string('customer_code', 50)->nullable()->change();
            $table->string('guest_link_code', 50)->nullable()->change();
            $table->string('currency_code', 50)->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('order_code', 50)->change();
            $table->string('product_code', 50)->change();
            $table->string('price_code', 50)->change();
        });

        Schema::table('prices', function (Blueprint $table) {
            $table->string('product_code', 50)->change();
            $table->string('currency_code', 50)->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('store_code', 50)->change();
            $table->string('category_code', 50)->change();
        });

        Schema::table('profits', function (Blueprint $table) {
            $table->string('store_code', 50)->change();
            $table->string('order_code', 50)->change();
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->string('store_code', 50)->change();
            $table->string('product_code', 50)->change();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('order_code', 50)->change();
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->string('user_code', 50)->change();
            $table->string('order_code', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Reverting this would mean putting back mismatched column types,
        // which is not strictly necessary. 
    }
};
