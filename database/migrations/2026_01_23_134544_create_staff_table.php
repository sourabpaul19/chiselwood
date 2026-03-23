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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('staff_id')->unique();
            $table->string('phone')->nullable();
            $table->foreignId('department_id')->nullable();
            $table->foreignId('employee_type_id')->nullable();
            $table->string('designation')->nullable();
            $table->string('skills')->nullable();
            $table->string('salary')->nullable();
            $table->string('document')->nullable();
            $table->text('notes')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
