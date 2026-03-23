<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_item_category', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inventory_item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('inventory_category_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', ['category', 'sub_category']);

            // SHORT index name (prevents MySQL 1059 error)
            $table->unique(
                ['inventory_item_id', 'inventory_category_id', 'type'],
                'item_cat_type_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_item_category');
    }
};
