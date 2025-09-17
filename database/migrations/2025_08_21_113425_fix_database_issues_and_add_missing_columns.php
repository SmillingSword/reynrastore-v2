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
        // Fix products table - add missing columns safely
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable()->after('name');
            }
            if (!Schema::hasColumn('products', 'has_price_lists')) {
                $table->boolean('has_price_lists')->default(false)->after('is_featured');
            }
            
            // Make slug nullable temporarily to fix existing records
            $table->string('slug')->nullable()->change();
        });

        // Fix orders table - ensure columns exist without duplicates
        Schema::table('orders', function (Blueprint $table) {
            // Check if notes column exists, if not add it
            if (!Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable()->after('customer_data');
            }
            
            // Check if snap_token exists, if not add it (it should exist from previous migration)
            if (!Schema::hasColumn('orders', 'snap_token')) {
                $table->string('snap_token')->nullable()->after('payment_status');
            }
        });

        // Update existing products to have slugs
        $products = \App\Models\Product::whereNull('slug')->orWhere('slug', '')->get();
        foreach ($products as $product) {
            $product->slug = $product->generateUniqueSlug($product->name);
            $product->save();
        }

        // Make slug required again after fixing existing records
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['brand', 'has_price_lists']);
        });
    }
};
