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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->string('address');
            $table->string('city');
            $table->decimal('latitude', 10, 7)->nullable(); // For geo-search
            $table->decimal('longitude', 10, 7)->nullable(); // For geo-search
            $table->json('opening_hours'); // Stores schedule: {"monday": {"open": "09:00", ...}}
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
