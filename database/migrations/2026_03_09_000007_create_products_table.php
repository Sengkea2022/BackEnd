<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('product_no', 50)->unique();
            $table->uuid('store_uuid');
            $table->uuid('category_uuid');
            $table->string('product_name');
            $table->text('description')->nullable();
            $table->string('image_path', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('store_uuid')
                  ->references('uuid')->on('stores')
                  ->onDelete('cascade');

            $table->foreign('category_uuid')
                  ->references('uuid')->on('categories')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
