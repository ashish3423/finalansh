<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            $table->boolean('aadhaar_document_status')->default(false);
            $table->string('aadhaar_document_path')->nullable();
            $table->boolean('pan_document_status')->default(false);
            $table->string('pan_document_path')->nullable();
            $table->boolean('bank_details_document_status')->default(false);
            $table->string('bank_details_document_path')->nullable();
            $table->boolean('tech_certificate_status')->default(false);
            $table->string('tech_certificate_path')->nullable();
            $table->boolean('education_certificate_status')->default(false);
            $table->string('education_certificate_path')->nullable();
            $table->string('police_verification_certificate_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            $table->dropColumn([
                'aadhaar_document_status', 'aadhaar_document_path', 'pan_document_status', 'pan_document_path',
                'bank_details_document_status', 'bank_details_document_path', 'tech_certificate_status', 'tech_certificate_path',
                'education_certificate_status', 'education_certificate_path', 'police_verification_certificate_path',
            ]);
        });
    }
};
