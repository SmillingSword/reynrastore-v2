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
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'diggie_transaction_id')) {
                $table->string('diggie_transaction_id')->nullable()->after('status');
            }
            if (!Schema::hasColumn('order_items', 'diggie_response')) {
                $table->json('diggie_response')->nullable()->after('diggie_transaction_id');
            }
            if (!Schema::hasColumn('order_items', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('diggie_response');
            }
            if (!Schema::hasColumn('order_items', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('processed_at');
            }
            if (!Schema::hasColumn('order_items', 'error_message')) {
                $table->text('error_message')->nullable()->after('completed_at');
            }
        });
        
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'snap_token')) {
                $table->string('snap_token')->nullable()->after('payment_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('snap_token');
        });
        
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'diggie_transaction_id', 
                'diggie_response', 
                'processed_at', 
                'completed_at', 
                'error_message'
            ]);
        });
    }
};
