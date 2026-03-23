<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('company_name')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('social_media')->nullable();
            $table->string('preferred_communication')->nullable();
            $table->string('projects')->nullable();
            $table->string('budget_range')->nullable();
            $table->text('notes')->nullable();
            $table->string('document')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'phone',
                'address',
                'social_media',
                'preferred_communication',
                'projects',
                'budget_range',
                'notes',
                'document'
            ]);
        });
    }
};
