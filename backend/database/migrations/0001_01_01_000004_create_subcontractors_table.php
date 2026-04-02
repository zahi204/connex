<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subcontractors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('registration_number', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->jsonb('specializations')->default('[]');
            $table->jsonb('operating_areas')->default('[]');
            $table->string('project_size', 20)->nullable();
            $table->integer('number_of_workers')->nullable();
            $table->integer('years_of_experience')->nullable();
            $table->text('notable_projects')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->string('status', 30)->default('available');
            $table->date('availability_date')->nullable();
            $table->text('general_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subcontractors');
    }
};
