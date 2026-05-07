<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->date('attendance_date'); // always the shift START date (handles overnight)
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->dateTime('login_time')->nullable();
            $table->dateTime('logout_time')->nullable();
            $table->unsignedSmallInteger('total_break_minutes')->default(0);
            $table->decimal('net_hours', 5, 2)->nullable(); // computed
            $table->enum('status', ['present', 'half_day', 'absent', 'holiday', 'weekend', 'leave', 'pending'])
                  ->default('pending');
            $table->boolean('is_late')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
