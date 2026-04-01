<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            // Drop foreign key first
            if (Schema::hasColumn('leads', 'project_type_id')) {
                $table->dropForeign(['project_type_id']);
                $table->dropColumn('project_type_id');
            }
        });
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'project_type_id')) {
                $table->unsignedBigInteger('project_type_id')->nullable();

                // Re-add foreign key (adjust table name if different)
                $table->foreign('project_type_id')
                      ->references('id')
                      ->on('project_types')
                      ->onDelete('cascade');
            }
        });
    }
};