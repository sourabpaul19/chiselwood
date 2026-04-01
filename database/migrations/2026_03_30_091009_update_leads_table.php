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
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('contact_details');
            $table->string('email')->nullable()->after('name');
            $table->string('phone', 20)->nullable()->after('email');
        });
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->text('contact_details')->nullable();
            $table->dropColumn(['email', 'phone']);
        });
    }
};
