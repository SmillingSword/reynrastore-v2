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
        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('diggie_sku_code')->unique(); // buyer_sku_code dari DigiFlazz
            $table->string('name'); // product_name dari DigiFlazz
            $table->string('type')->nullable(); // type dari DigiFlazz
            $table->text('description')->nullable(); // desc dari DigiFlazz
            $table->decimal('base_price', 15, 2); // price dari DigiFlazz
            $table->decimal('selling_price', 15, 2); // base_price + profit
            $table->decimal('profit_percentage', 5, 2)->default(15);
            $table->boolean('seller_status')->default(true); // seller_product_status
            $table->boolean('buyer_status')->default(true); // buyer_product_status
            $table->boolean('unlimited_stock')->default(true);
            $table->integer('stock')->default(0);
            $table->boolean('multi')->default(true); // bisa transaksi berulang
            $table->string('start_cut_off')->nullable();
            $table->string('end_cut_off')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['product_id', 'is_active']);
            $table->index(['diggie_sku_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_lists');
    }
};
