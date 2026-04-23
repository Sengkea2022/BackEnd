<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_links', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('link_no', 50)->unique();
            $table->uuid('store_uuid');
            $table->string('token', 100)->unique();
            $table->string('label')->nullable();
            $table->string('qr_path', 500)->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('store_uuid')
                  ->references('uuid')->on('stores')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_links');
    }
};
