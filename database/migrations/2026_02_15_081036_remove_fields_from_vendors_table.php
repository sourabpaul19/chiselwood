<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {

            if (Schema::hasColumn('vendors', 'rating')) {
                $table->dropColumn('rating');
            }

            if (Schema::hasColumn('vendors', 'payment_terms')) {
                $table->dropColumn('payment_terms');
            }

            if (Schema::hasColumn('vendors', 'gst_number')) {
                $table->dropColumn('gst_number');
            }
        });
    }

    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {

            if (!Schema::hasColumn('vendors', 'rating')) {
                $table->string('rating')->nullable();
            }

            if (!Schema::hasColumn('vendors', 'payment_terms')) {
                $table->string('payment_terms')->nullable();
            }

            if (!Schema::hasColumn('vendors', 'gst_number')) {
                $table->string('gst_number')->nullable();
            }
        });
    }
};
