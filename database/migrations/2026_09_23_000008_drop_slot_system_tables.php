<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('doctor_schedules');
        Schema::dropIfExists('appointment_slots');
    }

    public function down(): void
    {
        // Slot system removed intentionally.
    }
};
