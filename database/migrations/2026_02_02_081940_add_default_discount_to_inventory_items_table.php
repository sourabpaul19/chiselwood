<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->enum('discount_type', ['percent','flat'])->default('percent');
            $table->decimal('discount_value', 10, 2)->default(0);
        });
    }

    public function down()
    {
        Schema::table('inventory_items', function (Blueprint $table) {
        });
    }

};
