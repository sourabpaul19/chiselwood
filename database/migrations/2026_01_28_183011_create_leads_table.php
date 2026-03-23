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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_id')->unique();
            $table->string('name');
            $table->string('contact_details');
            $table->foreignId('lead_source_id')->constrained()->cascadeOnDelete();
            $table->date('inquiry_date')->nullable();
            $table->string('budget_expectation')->nullable();
            $table->foreignId('project_type_id')->constrained();
            $table->foreignId('lead_status_id')->constrained();
            $table->text('notes')->nullable();
            $table->dateTime('follow_up_date')->nullable();
            $table->foreignId('staff_id')->nullable()->constrained();
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
        Schema::dropIfExists('leads');
    }
};
