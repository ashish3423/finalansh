<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('organization', 180)->nullable()->after('contract_number');
            $table->string('department', 180)->nullable()->after('organization');
            $table->string('contract_category', 40)->nullable()->after('department');
            $table->text('address')->nullable()->after('contract_category');
            $table->string('city', 120)->nullable()->after('address');
            $table->string('state', 120)->nullable()->after('city');
            $table->string('postal_code', 20)->nullable()->after('state');
            $table->date('extension_date')->nullable()->after('end_date');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['organization', 'department', 'contract_category', 'address', 'city', 'state', 'postal_code', 'extension_date']);
        });
    }
};
