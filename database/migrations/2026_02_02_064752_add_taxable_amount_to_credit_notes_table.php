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
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->decimal('taxable_amount', 15, 2)->default(0);
        });
    }

    public function down()
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->dropColumn('taxable_amount');
        });
    }

};
