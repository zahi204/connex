<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_cycle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('worker_id')->constrained()->cascadeOnDelete();
            $table->boolean('attendance_day1')->default(false);
            $table->boolean('attendance_day2')->default(false);
            $table->decimal('professional_score', 5, 2)->nullable();
            $table->string('suitability', 20)->nullable();
            $table->string('classification', 50)->nullable();
            $table->string('primary_field', 50)->nullable();
            $table->text('professional_notes')->nullable();
            $table->text('placement_recommendation')->nullable();
            $table->string('certificate_path', 500)->nullable();
            $table->timestamps();

            $table->unique(['training_cycle_id', 'worker_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_results');
    }
};
