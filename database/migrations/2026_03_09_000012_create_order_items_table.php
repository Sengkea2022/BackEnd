<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('order_item_no', 50)->unique();
            $table->uuid('order_uuid');
            $table->uuid('product_uuid');
            $table->uuid('price_uuid');
            $table->integer('qty')->default(1);
            $table->boolean('is_wholesale')->default(false);
            $table->decimal('unit_price', 18, 4);
            $table->decimal('line_price', 18, 4);
            $table->timestamps();

            $table->foreign('order_uuid')
                  ->references('uuid')->on('orders')
                  ->onDelete('cascade');

            $table->foreign('product_uuid')
                  ->references('uuid')->on('products')
                  ->onDelete('restrict');

            $table->foreign('price_uuid')
                  ->references('uuid')->on('prices')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
