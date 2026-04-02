<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('resource_type', 100);
            $table->unsignedBigInteger('resource_id');
            $table->string('engagement_type', 20);
            $table->date('start_date')->nullable();
            $table->date('estimated_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->text('work_description')->nullable();
            $table->decimal('price_per_day', 10, 2)->nullable();
            $table->decimal('contract_amount', 12, 2)->nullable();
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->string('status', 30)->default('new');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('project_id');
            $table->index(['resource_type', 'resource_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
