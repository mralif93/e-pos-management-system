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
        Schema::table('outlets', function (Blueprint $table) {
            $table->string('outlet_code')->unique()->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outlets', function (Blueprint $table) {
            if (Schema::hasColumn('outlets', 'outlet_code')) { // Add this check
                $table->dropColumn('outlet_code');
            }
        });
    }
};
