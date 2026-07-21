<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_join_requests', function (Blueprint $table) {
            $table->string('user_code')->nullable()->after('user_id');
        });

        // Populate user_code for existing requests
        $requests = \App\Models\StoreJoinRequest::with('user')->get();
        foreach ($requests as $req) {
            if ($req->user && $req->user->code) {
                $req->update(['user_code' => $req->user->code]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('store_join_requests', function (Blueprint $table) {
            $table->dropColumn('user_code');
        });
    }
};
