<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('price_no', 50)->unique();
            $table->uuid('product_uuid');
            $table->uuid('currency_uuid');
            $table->decimal('cost_price', 18, 4)->default(0);
            $table->decimal('retail_unit_price', 18, 4);
            $table->decimal('wholesale_unit_price', 18, 4);
            $table->integer('min_wholesale_qty')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['product_uuid', 'currency_uuid']);

            $table->foreign('product_uuid')
                  ->references('uuid')->on('products')
                  ->onDelete('cascade');

            $table->foreign('currency_uuid')
                  ->references('uuid')->on('currencies')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
