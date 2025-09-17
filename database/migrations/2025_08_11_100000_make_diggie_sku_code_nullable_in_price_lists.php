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
        Schema::table('price_lists', function (Blueprint $table) {
            // Drop the unique constraint first
            $table->dropUnique(['diggie_sku_code']);
            
            // Make the column nullable
            $table->string('diggie_sku_code')->nullable()->change();
            
            // Add a new unique constraint that allows nulls
            $table->unique('diggie_sku_code', 'price_lists_diggie_sku_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_lists', function (Blueprint $table) {
            // Drop the nullable unique constraint
            $table->dropUnique('price_lists_diggie_sku_code_unique');
            
            // Make the column not nullable again
            $table->string('diggie_sku_code')->nullable(false)->change();
            
            // Add back the original unique constraint
            $table->unique('diggie_sku_code');
        });
    }
};
