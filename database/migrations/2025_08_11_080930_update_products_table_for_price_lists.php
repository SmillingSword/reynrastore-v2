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
        Schema::table('products', function (Blueprint $table) {
            // Make price columns nullable for brand-level products
            // Individual prices will be stored in price_lists table
            $table->decimal('base_price', 15, 2)->nullable()->change();
            $table->decimal('selling_price', 15, 2)->nullable()->change();
            
            // Add fields for better organization
            $table->string('brand')->nullable()->after('name'); // Brand name for grouping
            $table->boolean('has_price_lists')->default(false)->after('profit_percentage'); // Flag for products with price lists
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Revert price columns to NOT NULL (with default values)
            $table->decimal('base_price', 15, 2)->default(0)->change();
            $table->decimal('selling_price', 15, 2)->default(0)->change();
            
            // Remove added fields
            $table->dropColumn(['brand', 'has_price_lists']);
        });
    }
};
