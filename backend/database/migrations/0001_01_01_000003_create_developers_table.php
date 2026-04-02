<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('developers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('registration_number', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('logo', 500)->nullable();
            $table->text('company_description')->nullable();
            $table->jsonb('areas_of_operation')->default('[]');
            $table->string('company_size', 50)->nullable();
            $table->jsonb('specializations')->default('[]');
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_role', 100)->nullable();
            $table->string('contact_person_phone', 20)->nullable();
            $table->string('contact_person_email')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('developers');
    }
};
