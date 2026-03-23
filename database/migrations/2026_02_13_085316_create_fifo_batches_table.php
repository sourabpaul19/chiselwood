<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fifo_batches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inventory_item_id')->constrained();

            $table->decimal('qty_remaining', 12, 2);
            $table->decimal('unit_cost', 12, 2);

            $table->string('source_type')->nullable(); // opening / purchase / return
            $table->unsignedBigInteger('source_id')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fifo_batches');
    }
};
