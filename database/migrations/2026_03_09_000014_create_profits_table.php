<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profits', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('profit_no', 50)->unique();
            $table->uuid('store_uuid');
            $table->uuid('order_uuid')->unique(); // one profit record per order
            $table->decimal('total_cost', 18, 4);
            $table->decimal('total_revenue', 18, 4);
            $table->decimal('gross_profit', 18, 4);
            $table->date('profit_date');
            $table->timestamps();

            $table->foreign('store_uuid')
                  ->references('uuid')->on('stores')
                  ->onDelete('restrict');

            $table->foreign('order_uuid')
                  ->references('uuid')->on('orders')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profits');
    }
};
