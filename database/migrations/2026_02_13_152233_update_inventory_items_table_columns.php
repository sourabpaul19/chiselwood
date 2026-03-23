<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            
            // Rename current_stock to stocks
            $table->renameColumn('current_stock', 'stocks');

            // Drop opening_stock column
            $table->dropColumn('opening_stock');
        });
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            
            // Rollback changes
            $table->renameColumn('stocks', 'current_stock');
            $table->integer('opening_stock')->nullable();
        });
    }
};
