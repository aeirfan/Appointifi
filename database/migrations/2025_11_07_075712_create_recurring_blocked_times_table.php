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
        Schema::create('recurring_blocked_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('title')->nullable(); // e.g., "Lunch Break", "Prayer Time"
            $table->time('start_time'); // e.g., 13:00:00
            $table->time('end_time'); // e.g., 14:00:00
            $table->json('days_of_week'); // e.g., ["monday", "tuesday", "wednesday", "thursday", "friday"]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_blocked_times');
    }
};
