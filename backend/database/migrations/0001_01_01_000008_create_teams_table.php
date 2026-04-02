<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('team_type', 50)->nullable();
            $table->string('primary_field', 50)->nullable();
            $table->foreignId('team_leader_id')->nullable()->constrained('workers')->nullOnDelete();
            $table->foreignId('current_project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('operating_area', 20)->nullable();
            $table->string('status', 30)->default('available');
            $table->date('availability_date')->nullable();
            $table->string('work_types', 20)->default('both');
            $table->decimal('average_rating', 3, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('team_leader_id');
            $table->index('current_project_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
