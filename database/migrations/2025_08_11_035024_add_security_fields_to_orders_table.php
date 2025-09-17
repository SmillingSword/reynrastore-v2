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
            // Security signature fields
            $table->string('payment_signature')->nullable()->after('payment_data');
            $table->string('security_token')->nullable()->after('payment_signature');
            $table->string('qris_transaction_id')->nullable()->after('security_token');
            
            // Fraud detection fields
            $table->json('fraud_indicators')->nullable()->after('qris_transaction_id');
            $table->boolean('is_suspicious')->default(false)->after('fraud_indicators');
            $table->enum('security_status', ['pending', 'verified', 'flagged', 'blocked'])->default('pending')->after('is_suspicious');
            
            // Audit trail fields
            $table->string('client_ip')->nullable()->after('security_status');
            $table->text('user_agent')->nullable()->after('client_ip');
            $table->string('session_id')->nullable()->after('user_agent');
            $table->timestamp('security_verified_at')->nullable()->after('session_id');
            $table->string('verified_by')->nullable()->after('security_verified_at');
            
            // Payment integrity fields
            $table->string('payment_data_hash')->nullable()->after('verified_by');
            $table->timestamp('last_security_check')->nullable()->after('payment_data_hash');
            
            // Add indexes for performance
            $table->index(['security_status', 'created_at']);
            $table->index(['is_suspicious', 'created_at']);
            $table->index('qris_transaction_id');
            $table->index('client_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['security_status', 'created_at']);
            $table->dropIndex(['is_suspicious', 'created_at']);
            $table->dropIndex(['qris_transaction_id']);
            $table->dropIndex(['client_ip']);
            
            $table->dropColumn([
                'payment_signature',
                'security_token',
                'qris_transaction_id',
                'fraud_indicators',
                'is_suspicious',
                'security_status',
                'client_ip',
                'user_agent',
                'session_id',
                'security_verified_at',
                'verified_by',
                'payment_data_hash',
                'last_security_check'
            ]);
        });
    }
};
