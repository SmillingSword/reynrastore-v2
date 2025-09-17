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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_confirmed_by')->nullable()->after('payment_status');
            $table->timestamp('payment_confirmed_at')->nullable()->after('payment_confirmed_by');
            $table->text('payment_confirmation_notes')->nullable()->after('payment_confirmed_at');
            $table->boolean('auto_processed')->default(false)->after('payment_confirmation_notes');
            $table->timestamp('auto_processed_at')->nullable()->after('auto_processed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_confirmed_by',
                'payment_confirmed_at', 
                'payment_confirmation_notes',
                'auto_processed',
                'auto_processed_at'
            ]);
        });
    }
};
