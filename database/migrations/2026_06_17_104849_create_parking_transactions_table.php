<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parking_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slot_id')->constrained('parking_slots')->onDelete('cascade');
            $table->string('vehicle_plate', 20);
            $table->enum('vehicle_type', ['motor', 'mobil', 'truk']);
            $table->string('owner_name', 100)->nullable();
            $table->dateTime('entry_time');
            $table->dateTime('exit_time')->nullable();
            $table->decimal('snapshot_first_hour_rate', 8, 2);
            $table->decimal('snapshot_subsequent_hour_rate', 8, 2);
            $table->decimal('snapshot_daily_max_rate', 8, 2)->nullable();
            $table->decimal('snapshot_fine_lost_ticket', 8, 2)->default(0);
            $table->decimal('fee', 8, 2)->default(0);
            $table->string('payment_method', 30)->nullable();
            $table->string('officer_name', 50)->nullable();
            $table->enum('status', ['parked', 'exited', 'flagged'])->default('parked');
            $table->timestamps();
        });

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('ALTER TABLE parking_transactions ADD COLUMN duration_minutes INT GENERATED ALWAYS AS (0) STORED;');
        } else {
            DB::statement('ALTER TABLE parking_transactions ADD COLUMN duration_minutes INT GENERATED ALWAYS AS (TIMESTAMPDIFF(MINUTE, entry_time, exit_time)) STORED;');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_transactions');
    }
};
