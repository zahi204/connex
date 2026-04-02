<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('worker_id')->constrained()->cascadeOnDelete();
            $table->string('role', 50)->nullable();
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('left_at')->nullable();

            $table->index('team_id');
            $table->index('worker_id');
            // Unique constraint: worker can only be active on one team at a time
            // This is a partial unique index - handled at app level since standard unique doesn't support WHERE
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
