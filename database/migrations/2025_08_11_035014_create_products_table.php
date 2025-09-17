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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->json('images')->nullable(); // Multiple images
            $table->decimal('base_price', 15, 2); // Price from Diggie
            $table->decimal('selling_price', 15, 2); // Price with profit margin
            $table->decimal('profit_percentage', 5, 2)->default(0); // Profit margin %
            $table->string('sku')->unique()->nullable();
            $table->enum('type', ['diggie', 'manual'])->default('diggie'); // Product type
            $table->string('diggie_product_id')->nullable(); // Diggie API product ID
            $table->json('diggie_data')->nullable(); // Store Diggie API response
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('stock')->default(0); // For manual products
            $table->integer('sort_order')->default(0);
            $table->json('form_fields')->nullable(); // Custom form fields for order
            $table->text('instructions')->nullable(); // Instructions for manual processing
            $table->timestamps();
            
            $table->index(['category_id', 'is_active']);
            $table->index(['type', 'is_active']);
            $table->index('diggie_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
