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
        Schema::table('vendors', function (Blueprint $table) {
            //
            if (!Schema::hasColumn('inventory_items', 'vendor_id')) {
                $table->string('vendor_state', 50)->nullable()->after('address');
            }
            $table->string('pincode', 6);
            $table->string('gstin', 15)->nullable()->unique();
            $table->string('cin', 21)->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            //
        });
    }
};
