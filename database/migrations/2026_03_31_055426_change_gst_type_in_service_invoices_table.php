<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('service_invoices', function (Blueprint $table) {
            $table->string('gst_type')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('service_invoices', function (Blueprint $table) {
            $table->enum('gst_type', ['cgst','sgst','igst'])->nullable()->change();
        });
    }
};