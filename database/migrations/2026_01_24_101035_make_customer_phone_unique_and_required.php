<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First delete any customers with null phone to avoid conflict or update them
        // DB::table('customers')->whereNull('phone')->delete(); // Risky for prod, ok for dev if empty

        Schema::table('customers', function (Blueprint $table) {
            $table->string('phone')->nullable(false)->change(); // Make required
            $table->unique('phone'); // Make unique
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['phone']);
            $table->string('phone')->nullable()->change();
        });
    }
};
