<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('country')->nullable()->after('is_active');
            $table->string('state')->nullable()->after('country');    // province / state / region
            $table->string('city')->nullable()->after('state');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('country')->nullable()->after('store_no');
            $table->string('state')->nullable()->after('country');
            $table->string('city')->nullable()->after('state');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['country', 'state', 'city']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['country', 'state', 'city']);
        });
    }
};
