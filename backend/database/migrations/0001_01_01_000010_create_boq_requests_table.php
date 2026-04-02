<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boq_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requested_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('project_name');
            $table->string('city', 100)->nullable();
            $table->string('region', 20)->nullable();
            $table->string('project_type', 50)->nullable();
            $table->text('scope_description');
            $table->string('urgency', 20)->default('standard');
            $table->string('status', 30)->default('new');
            $table->decimal('price', 10, 2)->nullable();
            $table->foreignId('prepared_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('requested_by_user_id');
            $table->index('status');
            $table->index('prepared_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boq_requests');
    }
};
