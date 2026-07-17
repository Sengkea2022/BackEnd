<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kh_provinces', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();       // e.g. KH-15
            $table->string('name');
            $table->string('name_km')->nullable();
            $table->string('type')->default('province');
            $table->timestamps();
        });

        Schema::create('kh_districts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 15)->unique();       // e.g. KH-15-01
            $table->string('province_code', 10);
            $table->string('name');
            $table->string('name_km')->nullable();
            $table->string('type')->default('district');
            $table->timestamps();

            $table->foreign('province_code')->references('code')->on('kh_provinces')->onDelete('cascade');
        });

        Schema::create('kh_communes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();       // e.g. KH-15-01-001
            $table->string('district_code', 15);
            $table->string('name');
            $table->string('name_km')->nullable();
            $table->string('type')->default('commune');
            $table->timestamps();

            $table->foreign('district_code')->references('code')->on('kh_districts')->onDelete('cascade');
        });

        Schema::create('kh_villages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 25)->unique();       // e.g. KH-15-01-001-0001
            $table->string('commune_code', 20);
            $table->string('name');
            $table->string('name_km')->nullable();
            $table->timestamps();

            $table->foreign('commune_code')->references('code')->on('kh_communes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kh_villages');
        Schema::dropIfExists('kh_communes');
        Schema::dropIfExists('kh_districts');
        Schema::dropIfExists('kh_provinces');
    }
};
