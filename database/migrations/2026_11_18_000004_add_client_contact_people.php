<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('government_clients', function (Blueprint $table) {
            $table->string('contact_person_1')->nullable()->after('department');
            $table->string('contact_number_1', 30)->nullable()->after('contact_person_1');
            $table->string('contact_person_2')->nullable()->after('contact_number_1');
            $table->string('contact_number_2', 30)->nullable()->after('contact_person_2');
            $table->string('contact_person_3')->nullable()->after('contact_number_2');
            $table->string('contact_number_3', 30)->nullable()->after('contact_person_3');
            $table->string('contact_person_4')->nullable()->after('contact_number_3');
            $table->string('contact_number_4', 30)->nullable()->after('contact_person_4');
        });

        DB::table('government_clients')->whereNotNull('contact_person')->update(['contact_person_1' => DB::raw('contact_person')]);
        DB::table('government_clients')->whereNotNull('phone')->update(['contact_number_1' => DB::raw('phone')]);
    }

    public function down(): void
    {
        Schema::table('government_clients', function (Blueprint $table) {
            $table->dropColumn([
                'contact_person_1', 'contact_number_1',
                'contact_person_2', 'contact_number_2',
                'contact_person_3', 'contact_number_3',
                'contact_person_4', 'contact_number_4',
            ]);
        });
    }
};
