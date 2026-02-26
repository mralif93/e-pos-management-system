<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offline_sale_drafts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->json('cart_data');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('discount_reason')->nullable();
            $table->json('payments');
            $table->integer('points_earned')->default(0);
            $table->integer('points_redeemed')->default(0);
            $table->decimal('discount_from_points', 10, 2)->default(0);
            $table->timestamp('local_created_at');
            $table->boolean('synced')->default(false);
            $table->foreignId('synced_sale_id')->nullable()->constrained('sales')->onDelete('set null');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });

        Schema::create('offline_sale_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('draft_id');
            $table->foreign('draft_id')->references('id')->on('offline_sale_drafts')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offline_sale_items');
        Schema::dropIfExists('offline_sale_drafts');
    }
};
