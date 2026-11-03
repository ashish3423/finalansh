<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_rule_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('pf_employee_rate', 5, 2)->default(12.00);
            $table->decimal('pf_employer_rate', 5, 2)->default(12.00);
            $table->decimal('pf_monthly_wage_ceiling', 12, 2)->default(25000.00);
            $table->decimal('esi_employee_rate', 5, 2)->default(0.75);
            $table->decimal('esi_employer_rate', 5, 2)->default(3.25);
            $table->decimal('esi_monthly_wage_limit', 12, 2)->default(21000.00);
            $table->decimal('esi_employee_exemption_daily_wage', 10, 2)->default(176.00);
            $table->decimal('bonus_monthly_eligibility_limit', 12, 2)->default(0.00);
            $table->decimal('bonus_monthly_calculation_ceiling', 12, 2)->default(0.00);
            $table->decimal('bonus_minimum_rate', 5, 2)->default(8.33);
            $table->decimal('bonus_maximum_rate', 5, 2)->default(20.00);
            $table->unsignedSmallInteger('bonus_minimum_days')->default(30);
            $table->timestamps();
        });

        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->restrictOnDelete();
            $table->date('salary_month');
            $table->string('employee_code_snapshot', 40);
            $table->string('employee_name_snapshot', 160);
            $table->string('designation_snapshot', 100);
            $table->string('contract_number_snapshot', 80)->nullable();
            $table->unsignedSmallInteger('working_days');
            $table->decimal('daily_wages_rate', 12, 2);
            $table->decimal('earned_wages', 14, 2);
            $table->decimal('pf_wage_basis', 14, 2);
            $table->decimal('pf_employee_rate', 5, 2);
            $table->decimal('pf_employer_rate', 5, 2);
            $table->decimal('pf_employee_amount', 14, 2);
            $table->decimal('pf_employer_amount', 14, 2);
            $table->boolean('esi_applicable')->default(false);
            $table->decimal('esi_employee_rate', 5, 2);
            $table->decimal('esi_employer_rate', 5, 2);
            $table->decimal('esi_employee_amount', 14, 2);
            $table->decimal('esi_employer_amount', 14, 2);
            $table->boolean('bonus_eligible')->default(false);
            $table->unsignedSmallInteger('bonus_eligible_days')->default(0);
            $table->decimal('bonus_amount', 14, 2)->default(0);
            $table->decimal('gross_salary', 14, 2);
            $table->decimal('total_deductions', 14, 2);
            $table->decimal('net_salary', 14, 2);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['employee_id', 'salary_month']);
            $table->index(['salary_month', 'employee_code_snapshot']);
        });

        DB::table('salary_rule_settings')->insert([
            'pf_employee_rate' => 12.00,
            'pf_employer_rate' => 12.00,
            'pf_monthly_wage_ceiling' => 25000.00,
            'esi_employee_rate' => 0.75,
            'esi_employer_rate' => 3.25,
            'esi_monthly_wage_limit' => 21000.00,
            'esi_employee_exemption_daily_wage' => 176.00,
            'bonus_monthly_eligibility_limit' => 0.00,
            'bonus_monthly_calculation_ceiling' => 0.00,
            'bonus_minimum_rate' => 8.33,
            'bonus_maximum_rate' => 20.00,
            'bonus_minimum_days' => 30,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_salaries');
        Schema::dropIfExists('salary_rule_settings');
    }
};
