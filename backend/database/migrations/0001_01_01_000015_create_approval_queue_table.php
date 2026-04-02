<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submitted_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('approvable_type', 100);
            $table->unsignedBigInteger('approvable_id');
            $table->string('action_type', 30);
            $table->string('status', 30)->default('pending');
            $table->jsonb('submitted_data')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index('submitted_by_user_id');
            $table->index(['approvable_type', 'approvable_id']);
            $table->index('status');
            $table->index('action_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_queue');
    }
};
