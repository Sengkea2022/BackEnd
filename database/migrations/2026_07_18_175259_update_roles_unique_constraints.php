<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique('roles_name_unique');
            $table->dropUnique('roles_slug_unique');
            
            $table->unique(['name', 'store_code']);
            $table->unique(['slug', 'store_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique(['name', 'store_code']);
            $table->dropUnique(['slug', 'store_code']);
            
            $table->unique('name');
            $table->unique('slug');
        });
    }
};
