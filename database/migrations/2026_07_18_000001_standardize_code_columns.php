<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Own-code columns: *_no → code ────────────────────────────────────

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('user_no', 'code');
        });

        // users.store_no is a FK to stores.code
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('store_no', 'store_code');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->renameColumn('store_no', 'code');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('category_no', 'code');
        });

        Schema::table('currencies', function (Blueprint $table) {
            $table->renameColumn('currency_no', 'code');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('customer_no', 'code');
        });

        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->renameColumn('exchange_no', 'code');
        });

        Schema::table('guest_links', function (Blueprint $table) {
            $table->renameColumn('link_no', 'code');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('order_no', 'code');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('order_item_no', 'code');
        });

        Schema::table('prices', function (Blueprint $table) {
            $table->renameColumn('price_no', 'code');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('product_no', 'code');
        });

        Schema::table('profits', function (Blueprint $table) {
            $table->renameColumn('profit_no', 'code');
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->renameColumn('stock_no', 'code');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('transaction_no', 'code');
        });

        // ── FK columns: *_uuid → *_code ───────────────────────────────────────

        Schema::table('stores', function (Blueprint $table) {
            $table->renameColumn('user_uuid', 'user_code');
        });

        Schema::table('guest_links', function (Blueprint $table) {
            $table->renameColumn('store_uuid', 'store_code');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('store_uuid',      'store_code');
            $table->renameColumn('customer_uuid',   'customer_code');
            $table->renameColumn('guest_link_uuid', 'guest_link_code');
            $table->renameColumn('currency_uuid',   'currency_code');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('order_uuid',   'order_code');
            $table->renameColumn('product_uuid', 'product_code');
            $table->renameColumn('price_uuid',   'price_code');
        });

        Schema::table('prices', function (Blueprint $table) {
            $table->renameColumn('product_uuid',  'product_code');
            $table->renameColumn('currency_uuid', 'currency_code');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('store_uuid',    'store_code');
            $table->renameColumn('category_uuid', 'category_code');
        });

        Schema::table('profits', function (Blueprint $table) {
            $table->renameColumn('store_uuid', 'store_code');
            $table->renameColumn('order_uuid', 'order_code');
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->renameColumn('store_uuid',   'store_code');
            $table->renameColumn('product_uuid', 'product_code');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('order_uuid', 'order_code');
        });

        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->renameColumn('from_currency_uuid', 'from_currency_code');
            $table->renameColumn('to_currency_uuid',   'to_currency_code');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->renameColumn('user_uuid',  'user_code');
            $table->renameColumn('order_uuid', 'order_code');
        });
    }

    public function down(): void
    {
        // Reverse: code → *_no
        Schema::table('users',        fn($t) => $t->renameColumn('code', 'user_no'));
        Schema::table('stores',       fn($t) => $t->renameColumn('code', 'store_no'));
        Schema::table('categories',   fn($t) => $t->renameColumn('code', 'category_no'));
        Schema::table('currencies',   fn($t) => $t->renameColumn('code', 'currency_no'));
        Schema::table('customers',    fn($t) => $t->renameColumn('code', 'customer_no'));
        Schema::table('exchange_rates',fn($t) => $t->renameColumn('code', 'exchange_no'));
        Schema::table('guest_links',  fn($t) => $t->renameColumn('code', 'link_no'));
        Schema::table('orders',       fn($t) => $t->renameColumn('code', 'order_no'));
        Schema::table('order_items',  fn($t) => $t->renameColumn('code', 'order_item_no'));
        Schema::table('prices',       fn($t) => $t->renameColumn('code', 'price_no'));
        Schema::table('products',     fn($t) => $t->renameColumn('code', 'product_no'));
        Schema::table('profits',      fn($t) => $t->renameColumn('code', 'profit_no'));
        Schema::table('stocks',       fn($t) => $t->renameColumn('code', 'stock_no'));
        Schema::table('transactions', fn($t) => $t->renameColumn('code', 'transaction_no'));

        // Reverse: *_code → *_uuid
        Schema::table('stores',       fn($t) => $t->renameColumn('user_code',     'user_uuid'));
        Schema::table('guest_links',  fn($t) => $t->renameColumn('store_code',    'store_uuid'));
        Schema::table('orders',       function($t) {
            $t->renameColumn('store_code',      'store_uuid');
            $t->renameColumn('customer_code',   'customer_uuid');
            $t->renameColumn('guest_link_code', 'guest_link_uuid');
            $t->renameColumn('currency_code',   'currency_uuid');
        });
        Schema::table('order_items',  function($t) {
            $t->renameColumn('order_code',   'order_uuid');
            $t->renameColumn('product_code', 'product_uuid');
            $t->renameColumn('price_code',   'price_uuid');
        });
        Schema::table('prices',       function($t) {
            $t->renameColumn('product_code',  'product_uuid');
            $t->renameColumn('currency_code', 'currency_uuid');
        });
        Schema::table('products',     function($t) {
            $t->renameColumn('store_code',    'store_uuid');
            $t->renameColumn('category_code', 'category_uuid');
        });
        Schema::table('profits',      function($t) {
            $t->renameColumn('store_code', 'store_uuid');
            $t->renameColumn('order_code', 'order_uuid');
        });
        Schema::table('stocks',       function($t) {
            $t->renameColumn('store_code',   'store_uuid');
            $t->renameColumn('product_code', 'product_uuid');
        });
        Schema::table('transactions', fn($t) => $t->renameColumn('order_code',  'order_uuid'));
        Schema::table('exchange_rates', function($t) {
            $t->renameColumn('from_currency_code', 'from_currency_uuid');
            $t->renameColumn('to_currency_code',   'to_currency_uuid');
        });
        Schema::table('notifications', function($t) {
            $t->renameColumn('user_code',  'user_uuid');
            $t->renameColumn('order_code', 'order_uuid');
        });
    }
};
