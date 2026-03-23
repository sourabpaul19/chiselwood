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
        Schema::create('inventory_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_vendor_id')->unique();

            // 3️⃣ link user
            $table->foreignId('user_id')->constrained('users')
                  ->cascadeOnDelete();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('inventory_vendor_state', 50)->nullable();
            $table->foreignId('inventory_vendor_category_id')->constrained()->cascadeOnDelete();
            $table->string('pincode', 6);
            $table->string('gstin', 15)->nullable()->unique();
            $table->string('cin', 21)->nullable()->unique();
            $table->string('payment_terms')->nullable();
            $table->tinyInteger('rating')->default(0);
            $table->text('notes')->nullable();
            $table->string('document')->nullable();
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
        Schema::dropIfExists('inventory_vendors');
    }
};
