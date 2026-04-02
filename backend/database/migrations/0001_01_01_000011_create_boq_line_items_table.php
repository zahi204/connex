<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boq_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boq_request_id')->constrained()->cascadeOnDelete();
            $table->integer('line_number');
            $table->string('trade', 50);
            $table->text('description');
            $table->string('unit_of_measure', 30);
            $table->decimal('quantity', 12, 3);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('total_cost', 14, 2)->nullable();
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('boq_request_id');
            $table->index('trade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boq_line_items');
    }
};
