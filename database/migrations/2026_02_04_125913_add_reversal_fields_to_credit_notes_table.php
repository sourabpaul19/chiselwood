<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->foreignId('original_credit_note_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('credit_notes')
                  ->nullOnDelete();

            $table->boolean('locked')
                  ->default(false)
                  ->after('status');

            $table->boolean('reversal_created')
                  ->default(false)
                  ->after('locked');
        });
    }

    public function down(): void
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->dropColumn(['original_credit_note_id', 'locked', 'reversal_created']);
        });
    }
};
