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
        Schema::create('inventory_batches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inventory_item_id')->constrained();

            $table->decimal('quantity', 12, 2);
            $table->decimal('remaining_quantity', 12, 2);

            $table->decimal('unit_cost', 12, 2);

            $table->string('reference_type');
            $table->unsignedBigInteger('reference_id');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_batches');
    }
};
