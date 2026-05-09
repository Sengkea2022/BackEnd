<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('exchange_no', 50)->unique();
            $table->uuid('from_currency_uuid');
            $table->uuid('to_currency_uuid');
            $table->decimal('rate', 18, 6);
            $table->date('exchange_date');
            $table->timestamps();

            $table->unique(['from_currency_uuid', 'to_currency_uuid', 'exchange_date'], 'exchange_rates_pair_date_unique');

            $table->foreign('from_currency_uuid')
                  ->references('uuid')->on('currencies')
                  ->onDelete('restrict');

            $table->foreign('to_currency_uuid')
                  ->references('uuid')->on('currencies')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};