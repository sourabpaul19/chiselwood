<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {

            // 1️⃣ remove vendor_uid
            if (Schema::hasColumn('vendors', 'vendor_uid')) {
                $table->dropColumn('vendor_uid');
            }

            // 2️⃣ add vendor_id (human readable)
            $table->string('vendor_id')->unique()->after('id');

            // 3️⃣ link user
            $table->foreignId('user_id')
                  ->after('vendor_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('vendor_uid')->nullable();
            $table->dropForeign(['user_id']);
            $table->dropColumn(['vendor_id', 'user_id']);
        });
    }
};
