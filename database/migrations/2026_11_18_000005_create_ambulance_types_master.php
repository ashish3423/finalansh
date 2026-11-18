<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ambulance_types', function (Blueprint $table) {
            $table->id();
            $table->string('category', 20);
            $table->string('name', 120);
            $table->timestamps();
            $table->index(['category', 'name']);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->unsignedSmallInteger('year')->nullable()->after('service_type');
            $table->foreignId('ambulance_type_id')->nullable()->after('year')->constrained('ambulance_types')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ambulance_type_id');
            $table->dropColumn('year');
        });

        Schema::dropIfExists('ambulance_types');
    }
};
