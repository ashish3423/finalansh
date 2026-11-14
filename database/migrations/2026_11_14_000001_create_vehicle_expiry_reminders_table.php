<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_expiry_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('expiry_type', 20);
            $table->date('expiry_date');
            $table->date('remind_on');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'vehicle_id', 'expiry_type', 'expiry_date'], 'vehicle_expiry_reminders_unique');
            $table->index(['user_id', 'closed_at', 'remind_on'], 'vehicle_expiry_reminders_due_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_expiry_reminders');
    }
};
