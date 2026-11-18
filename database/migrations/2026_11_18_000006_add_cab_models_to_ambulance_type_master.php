<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('ambulance_types', 'service_type')) {
            Schema::table('ambulance_types', function (Blueprint $table) {
                $table->string('service_type', 20)->default('ambulance')->after('id');
            });
        }

        Schema::table('ambulance_types', function (Blueprint $table) {
            $table->string('category', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        $cabModelIds = DB::table('ambulance_types')->where('service_type', 'cab')->pluck('id');
        if ($cabModelIds->isNotEmpty()) {
            DB::table('vehicles')->whereIn('ambulance_type_id', $cabModelIds)->update(['ambulance_type_id' => null]);
            DB::table('ambulance_types')->whereIn('id', $cabModelIds)->delete();
        }

        Schema::table('ambulance_types', function (Blueprint $table) {
            $table->string('category', 20)->nullable(false)->change();
            $table->dropColumn('service_type');
        });
    }
};
