<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staffing_agencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('agency_name');
            $table->string('registration_number', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_role', 100)->nullable();
            $table->string('contact_person_phone', 20)->nullable();
            $table->string('contact_person_email')->nullable();
            $table->jsonb('worker_types')->default('[]');
            $table->jsonb('countries_of_origin')->default('[]');
            $table->integer('average_capacity')->nullable();
            $table->integer('monthly_throughput')->nullable();
            $table->integer('workers_transferred')->default(0);
            $table->integer('workers_trained')->default(0);
            $table->decimal('average_quality', 3, 2)->nullable();
            $table->decimal('payments_made', 12, 2)->default(0);
            $table->decimal('outstanding_balance', 12, 2)->default(0);
            $table->text('general_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staffing_agencies');
    }
};
