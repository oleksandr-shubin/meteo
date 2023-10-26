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
        Schema::create('average_weather_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained();
            $table->float('precipitation_mm');
            $table->float('uv');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('average_weather_states');
    }
};
