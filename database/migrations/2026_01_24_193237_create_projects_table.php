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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            // Relations
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_status_id')->constrained()->cascadeOnDelete();

            // Dates
            $table->date('start_date')->nullable();
            $table->date('estimated_end_date')->nullable();
            $table->date('actual_end_date')->nullable();

            // Budget
            $table->decimal('estimated_budget', 12, 2)->nullable();
            $table->decimal('actual_cost', 12, 2)->nullable();

            $table->string('location')->nullable();

            // Progress %
            $table->unsignedTinyInteger('progress')->default(0);

            // Image / Drawing
            $table->string('design_file')->nullable();

            // Notes
            $table->text('notes')->nullable();

            // Publish status
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
