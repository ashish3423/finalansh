<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->unsignedInteger('number_of_manpower')->default(0)->after('service_type');
            $table->unsignedInteger('number_of_ambulance')->default(0)->after('number_of_manpower');
            $table->string('ambulance_type', 120)->nullable()->after('number_of_ambulance');
            $table->string('billing_cycle', 30)->default('monthly')->after('ambulance_type');
            $table->decimal('monthly_cost', 14, 2)->default(0)->after('billing_cycle');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'number_of_manpower',
                'number_of_ambulance',
                'ambulance_type',
                'billing_cycle',
                'monthly_cost',
            ]);
        });
    }
};
