<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('order_no', 50)->unique();
            $table->uuid('store_uuid');
            $table->uuid('customer_uuid');
            $table->uuid('guest_link_uuid')->nullable();
            $table->uuid('currency_uuid');
            $table->enum('status', [
                'pending',
                'confirmed',
                'processing',
                'completed',
                'cancelled',
                'returned',
            ])->default('pending');
            $table->text('note')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->foreign('store_uuid')
                  ->references('uuid')->on('stores')
                  ->onDelete('restrict');

            $table->foreign('customer_uuid')
                  ->references('uuid')->on('customers')
                  ->onDelete('restrict');

            $table->foreign('guest_link_uuid')
                  ->references('uuid')->on('guest_links')
                  ->onDelete('set null');

            $table->foreign('currency_uuid')
                  ->references('uuid')->on('currencies')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
