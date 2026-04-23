<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('store_no', 50)->unique();
            $table->uuid('user_uuid');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('logo_path', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('user_uuid')
                  ->references('uuid')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
