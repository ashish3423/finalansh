<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('salary_rule_settings')->update([
            'bonus_monthly_eligibility_limit' => 0,
            'bonus_monthly_calculation_ceiling' => 0,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('salary_rule_settings')->update([
            'bonus_monthly_eligibility_limit' => 21000,
            'bonus_monthly_calculation_ceiling' => 7000,
            'updated_at' => now(),
        ]);
    }
};
