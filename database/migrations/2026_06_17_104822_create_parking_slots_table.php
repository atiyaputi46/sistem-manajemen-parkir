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
        Schema::create('parking_slots', function (Blueprint $table) {
            $table->id();
            $table->string('slot_code', 20)->unique();
            $table->enum('vehicle_type', ['motor', 'mobil', 'truk']);
            $table->string('floor', 10)->default('1');
            $table->string('zone', 20)->nullable();
            $table->enum('status', ['available', 'occupied', 'reserved', 'disabled'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_slots');
    }
};
