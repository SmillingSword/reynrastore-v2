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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('product_name'); // Store product name at time of order
            $table->string('product_sku')->nullable();
            $table->decimal('unit_price', 15, 2);
            $table->integer('quantity');
            $table->decimal('total_price', 15, 2);
            $table->json('product_data')->nullable(); // Store product details at time of order
            $table->json('form_data')->nullable(); // Store customer form data for this item
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('diggie_transaction_id')->nullable(); // Diggie transaction ID
            $table->json('diggie_response')->nullable(); // Store Diggie API response
            $table->text('processing_notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index(['order_id', 'status']);
            $table->index('diggie_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
