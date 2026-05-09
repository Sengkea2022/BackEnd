<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('stock_no', 50)->unique();
            $table->uuid('store_uuid');
            $table->uuid('product_uuid');
            $table->integer('qty')->default(0);
            $table->integer('low_stock_alert_qty')->default(5);
            $table->timestamps();

            $table->unique(['store_uuid', 'product_uuid']);

            $table->foreign('store_uuid')
                  ->references('uuid')->on('stores')
                  ->onDelete('cascade');

            $table->foreign('product_uuid')
                  ->references('uuid')->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
