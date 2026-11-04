<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('employee_category', 30)->nullable()->after('role');
            $table->string('work_location', 180)->nullable()->after('employee_category');
            $table->string('department', 160)->nullable()->after('work_location');
            $table->string('aadhar_number', 20)->nullable()->after('department');
            $table->string('account_number', 40)->nullable()->after('aadhar_number');
            $table->string('ifsc_code', 20)->nullable()->after('account_number');
            $table->string('uan_number', 30)->nullable()->after('ifsc_code');
            $table->string('esi_number', 30)->nullable()->after('uan_number');
            $table->string('ref_num1', 100)->nullable()->after('esi_number');
            $table->string('ref_num2', 100)->nullable()->after('ref_num1');
            $table->string('id_card_status', 20)->nullable()->after('ref_num2');
            $table->string('dress_status', 20)->nullable()->after('id_card_status');
            $table->boolean('epf_exempt')->default(false)->after('dress_status');
            $table->boolean('esi_exempt')->default(false)->after('epf_exempt');
            $table->string('police_verification_status', 10)->default('no')->after('esi_exempt');
            $table->string('police_verification_certificate_number', 100)->nullable()->after('police_verification_status');
        });

        Schema::table('employee_salaries', function (Blueprint $table) {
            $table->boolean('epf_exempt')->default(false)->after('pf_wage_basis');
            $table->boolean('esi_exempt')->default(false)->after('esi_applicable');
        });
    }

    public function down(): void
    {
        Schema::table('employee_salaries', function (Blueprint $table) {
            $table->dropColumn(['epf_exempt', 'esi_exempt']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'employee_category', 'work_location', 'department', 'aadhar_number', 'account_number', 'ifsc_code',
                'uan_number', 'esi_number', 'ref_num1', 'ref_num2', 'id_card_status', 'dress_status',
                'epf_exempt', 'esi_exempt', 'police_verification_status', 'police_verification_certificate_number',
            ]);
        });
    }
};
