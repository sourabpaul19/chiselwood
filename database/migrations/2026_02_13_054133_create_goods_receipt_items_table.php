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
        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('goods_receipt_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedBigInteger('inventory_item_id');
            
            $table->foreignId('purchase_order_item_id')
                ->constrained();

            $table->decimal('received_quantity', 12, 2);
            
            $table->decimal('unit_cost', 15, 2);

            $table->decimal('selling_price', 15, 2)
                ->nullable();

            $table->decimal('total', 15, 2);

            $table->foreign('inventory_item_id')
                ->references('id')
                ->on('inventory_items')
                ->onDelete('cascade');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_items');
    }
};
