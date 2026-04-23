<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('currency_no', 50)->unique();
            $table->string('country_code', 10);
            $table->string('currency_code', 10)->unique();
            $table->string('currency_name', 100);
            $table->string('symbol', 10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
