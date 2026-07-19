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
        Schema::table('store_join_requests', function (Blueprint $table) {
            $table->string('type')->default('request')->after('store_id')->comment('request or invite');
            $table->unsignedBigInteger('role_id')->nullable()->after('type');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_join_requests', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['type', 'role_id']);
        });
    }
};
