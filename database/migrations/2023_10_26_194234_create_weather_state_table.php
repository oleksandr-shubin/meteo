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
        Schema::create('weather_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained();
            $table->string('provider_name');
            $table->integer('precipitation_mm');
            $table->integer('uv');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_states');
    }
};
