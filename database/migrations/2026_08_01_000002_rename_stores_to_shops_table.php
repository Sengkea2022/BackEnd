<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rename stores table to shops
        if (Schema::hasTable('stores') && !Schema::hasTable('shops')) {
            Schema::rename('stores', 'shops');
        }

        // 2. Rename store_join_requests table to shop_join_requests
        if (Schema::hasTable('store_join_requests') && !Schema::hasTable('shop_join_requests')) {
            Schema::rename('store_join_requests', 'shop_join_requests');
        }

        // 3. Rename columns in shop_join_requests
        if (Schema::hasTable('shop_join_requests')) {
            Schema::table('shop_join_requests', function (Blueprint $table) {
                if (Schema::hasColumn('shop_join_requests', 'store_id') && !Schema::hasColumn('shop_join_requests', 'shop_id')) {
                    $table->renameColumn('store_id', 'shop_id');
                }
            });
        }

        // 4. Rename store_code -> shop_code in tables
        $tablesWithStoreCode = ['users', 'products', 'orders', 'stocks', 'profits', 'roles', 'guest_links'];
        foreach ($tablesWithStoreCode as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'store_code') && !Schema::hasColumn($tableName, 'shop_code')) {
                        $table->renameColumn('store_code', 'shop_code');
                    }
                });
            }
        }

        // 5. Rename store_uuid -> shop_uuid in guest_links
        if (Schema::hasTable('guest_links')) {
            Schema::table('guest_links', function (Blueprint $table) {
                if (Schema::hasColumn('guest_links', 'store_uuid') && !Schema::hasColumn('guest_links', 'shop_uuid')) {
                    $table->renameColumn('store_uuid', 'shop_uuid');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('shops') && !Schema::hasTable('stores')) {
            Schema::rename('shops', 'stores');
        }

        if (Schema::hasTable('shop_join_requests') && !Schema::hasTable('store_join_requests')) {
            Schema::rename('shop_join_requests', 'store_join_requests');
        }

        if (Schema::hasTable('store_join_requests')) {
            Schema::table('store_join_requests', function (Blueprint $table) {
                if (Schema::hasColumn('store_join_requests', 'shop_id') && !Schema::hasColumn('store_join_requests', 'store_id')) {
                    $table->renameColumn('shop_id', 'store_id');
                }
            });
        }

        $tablesWithShopCode = ['users', 'products', 'orders', 'stocks', 'profits', 'roles', 'guest_links'];
        foreach ($tablesWithShopCode as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'shop_code') && !Schema::hasColumn($tableName, 'store_code')) {
                        $table->renameColumn('shop_code', 'store_code');
                    }
                });
            }
        }

        if (Schema::hasTable('guest_links')) {
            Schema::table('guest_links', function (Blueprint $table) {
                if (Schema::hasColumn('guest_links', 'shop_uuid') && !Schema::hasColumn('guest_links', 'store_uuid')) {
                    $table->renameColumn('shop_uuid', 'store_uuid');
                }
            });
        }
    }
};
