<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->integer('loyalty_points')->default(0)->after('email');
            $table->integer('total_points_earned')->default(0)->after('loyalty_points');
            $table->enum('loyalty_tier', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze')->after('total_points_earned');
            $table->date('points_expiry_date')->nullable()->after('loyalty_tier');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->integer('points_earned')->default(0)->after('discount_reason');
            $table->integer('points_redeemed')->default(0)->after('points_earned');
            $table->decimal('discount_from_points', 10, 2)->default(0)->after('points_redeemed');
        });

        Schema::create('loyalty_point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('sale_id')->nullable()->constrained('sales')->onDelete('set null');
            $table->enum('type', ['earn', 'redeem', 'expire', 'adjust']);
            $table->integer('points');
            $table->integer('points_balance_after');
            $table->string('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_point_transactions');
        
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['points_earned', 'points_redeemed', 'discount_from_points']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['loyalty_points', 'total_points_earned', 'loyalty_tier', 'points_expiry_date']);
        });
    }
};
