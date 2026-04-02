<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payable_type', 100);
            $table->unsignedBigInteger('payable_id');
            $table->string('payment_type', 30);
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->foreignId('recorded_by_user_id')->constrained('users')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['payable_type', 'payable_id']);
            $table->index('payment_type');
            $table->index('recorded_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
