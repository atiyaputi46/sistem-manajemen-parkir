<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_change_logs', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_type', 10);
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->json('old_rates');
            $table->json('new_rates');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_change_logs');
    }
};
