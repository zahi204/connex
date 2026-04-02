<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('staffing_agency_id')->nullable()->constrained('staffing_agencies')->nullOnDelete();
            $table->string('full_name');
            $table->string('id_number', 50)->nullable();
            $table->string('photo', 500)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('country_of_origin', 100)->nullable();
            $table->jsonb('languages')->default('[]');
            $table->date('date_of_arrival')->nullable();
            $table->string('primary_skill', 50)->nullable();
            $table->string('secondary_skill', 50)->nullable();
            $table->string('preferred_work_area', 20)->nullable();
            $table->boolean('available_daily')->default(true);
            $table->boolean('available_contract')->default(true);
            $table->decimal('training_score', 5, 2)->nullable();
            $table->decimal('professional_rating', 3, 2)->nullable();
            $table->decimal('reliability_rating', 3, 2)->nullable();
            $table->boolean('suitable_for_leader')->default(false);
            $table->text('previous_experience')->nullable();
            $table->string('status', 30)->default('available');
            $table->string('payment_status', 20)->default('paid');
            $table->date('last_payment_date')->nullable();
            $table->boolean('eligible_for_assignment')->default(true);
            $table->boolean('blocked')->default(false);
            $table->text('general_notes')->nullable();
            $table->date('last_training_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('staffing_agency_id');
            $table->index('status');
            $table->index('primary_skill');
            $table->index('preferred_work_area');
            $table->index('professional_rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
