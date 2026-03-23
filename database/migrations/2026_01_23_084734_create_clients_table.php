<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            $table->string('client_id')->unique(); // AUTO00001
            $table->string('name');
            $table->string('email')->unique();

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
            $table->softDeletes(); // trash / restore
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};