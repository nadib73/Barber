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
        Schema::create('kapster_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kapster_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('day_of_week'); // 0 = Sunday, 1 = Monday, dst
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kapster_schedules');
    }
};
