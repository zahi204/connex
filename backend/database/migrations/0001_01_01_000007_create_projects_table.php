<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by_user_id')->constrained('users')->restrictOnDelete();
            $table->string('name');
            $table->string('address', 500)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('region', 20)->nullable();
            $table->string('project_type', 50);
            $table->date('estimated_start_date')->nullable();
            $table->date('estimated_completion')->nullable();
            $table->string('status', 30)->default('new');
            $table->string('source', 20)->default('manual');
            $table->jsonb('required_trades')->default('[]');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('developer_id');
            $table->index('created_by_user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
