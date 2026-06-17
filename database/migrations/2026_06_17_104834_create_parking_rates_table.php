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
        Schema::create('parking_rates', function (Blueprint $table) {
            $table->id();
            $table->enum('vehicle_type', ['motor', 'mobil', 'truk']);
            $table->decimal('first_hour_rate', 8, 2);
            $table->decimal('subsequent_hour_rate', 8, 2);
            $table->decimal('daily_max_rate', 8, 2)->nullable();
            $table->decimal('fine_lost_ticket', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_rates');
    }
};
