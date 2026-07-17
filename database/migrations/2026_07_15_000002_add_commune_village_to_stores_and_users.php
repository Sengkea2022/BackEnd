<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('commune')->nullable()->after('city');
            $table->string('village')->nullable()->after('commune');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('commune')->nullable()->after('city');
            $table->string('village')->nullable()->after('commune');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['commune', 'village']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['commune', 'village']);
        });
    }
};
