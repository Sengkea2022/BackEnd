<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('user_uuid');
            $table->uuid('order_uuid');
            $table->string('type', 100);
            $table->string('title');
            $table->text('body');
            $table->dateTime('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_uuid')
                  ->references('uuid')->on('users')
                  ->onDelete('cascade');

            $table->foreign('order_uuid')
                  ->references('uuid')->on('orders')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
