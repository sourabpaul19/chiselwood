<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            // Add gst_type column
            $table->string('gst_type', 50)->after('igst')->nullable()->default(''); 
            
            // Add status column
            $table->string('status', 20)->after('reason')->nullable()->default('active');
        });
    }

    public function down()
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->dropColumn(['gst_type', 'status']);
        });
    }
};
