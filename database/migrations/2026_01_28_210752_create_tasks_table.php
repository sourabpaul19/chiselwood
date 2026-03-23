<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_id')->unique(); // auto-generated Task ID
            $table->string('title');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('priority_id')->nullable()->constrained('task_priorities')->onDelete('set null');
            $table->foreignId('status_id')->nullable()->constrained('task_statuses')->onDelete('set null');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->text('description')->nullable();
            $table->json('documents')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();
        });

    }

    public function down(): void {
        Schema::dropIfExists('tasks');
    }
};
