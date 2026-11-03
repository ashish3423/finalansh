<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('salary_rule_settings')->update([
            'pf_monthly_wage_ceiling' => 25000,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('salary_rule_settings')->update([
            'pf_monthly_wage_ceiling' => 15000,
            'updated_at' => now(),
        ]);
    }
};
