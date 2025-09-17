<?php

namespace App\Services;

use App\Models\Order;
use App\Services\EnhancedSecurityService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Midtrans\SnapBi;
use Midtrans\Config;
use Exception;

class QrisService
{
    protected $securityService;
    protected $config;

    public function __construct(EnhancedSecurityService $securityService)
    {
        $this->securityService = $securityService;
        $this->config = config('qris');
        
        // Configure Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Create QRIS payment
     */
    public function createQrisPayment(Order $order): array
    {
        try {
            // Validate order
            $this->validateOrder($order);
            
            // Generate secure transaction ID
            $qrisTransactionId = $this->securityService->generateQrisTransactionId($order->order_number);
            
            // Prepare payment data
            $paymentData = $this->preparePaymentData($order, $qrisTransactionId);
            
            // Perform fraud detection
            $fraudIndicators = $this->securityService->detectQrisPaymentFraud($paymentData);
            
            // Log security event
            $this->securityService->logQrisSecurityEvent('qris_payment_creation_attempt', [
                'order_number' => $order->order_number,
                'amount' => $paymentData['amount'],
                'qris_transaction_id' => $qrisTransactionId,
                'fraud_indicators' => $fraudIndicators,
            ]);
            
            // Create QRIS payment via Midtrans SnapBi
            $qrisResponse = $this->createMidtransQrisPayment($paymentData);
            
            // Generate payment signature
            $paymentSignature = $this->securityService->generateQrisPaymentSignature($paymentData);
            
            // Update order with QRIS data
            $this->updateOrderWithQrisData($order, $qrisResponse, $qrisTransactionId, $paymentSignature, $fraudIndicators);
            
            // Prepare response
            $response = [
                'success' => true,
                'qris_transaction_id' => $qrisTransactionId,
                'qr_string' => $qrisResponse['qr_string'] ?? null,
                'qr_code_url' => $qrisResponse['qr_code_url'] ?? null,
                'amount' => $paymentData['amount'],
                'expiry_time' => $qrisResponse['expiry_time'] ?? now()->addMinutes(15)->toISOString(),
                'payment_reference' => $qrisResponse['reference_id'] ?? $qrisTransactionId,
                'fraud_indicators' => $fraudIndicators,
                'requires_manual_review' => !empty($fraudIndicators),
            ];
            
            // Cache payment data for quick access
            $this->cachePaymentData($qrisTransactionId, $response);
            
            return $response;
            
        } catch (Exception $e) {
            $this->securityService->logQrisSecurityEvent('qris_payment_creation_failed', [
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Check QRIS payment status
     */
    public function checkQrisPaymentStatus(string $qrisTransactionId): array
    {
        try {
            // Check cache first
            $cachedStatus = $this->getCachedPaymentStatus($qrisTransactionId);
            if ($cachedStatus) {
                return $cachedStatus;
            }
            
            // Get status from Midtrans
            $statusResponse = $this->getMidtransQrisStatus($qrisTransactionId);
            
            // Prepare response
            $response = [
                'success' => true,
                'qris_transaction_id' => $qrisTransactionId,
                'status' => $statusResponse['transaction_status'] ?? 'pending',
                'payment_method' => $statusResponse['payment_type'] ?? 'qris',
                'amount' => $statusResponse['gross_amount'] ?? 0,
                'paid_at' => $statusResponse['transaction_time'] ?? null,
                'reference_id' => $statusResponse['reference_id'] ?? null,
            ];
            
            // Cache status for performance
            $this->cachePaymentStatus($qrisTransactionId, $response);
            
            return $response;
            
        } catch (Exception $e) {
            $this->securityService->logQrisSecurityEvent('qris_status_check_failed', [
                'qris_transaction_id' => $qrisTransactionId,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle QRIS webhook notification
     */
    public function handleQrisWebhook(array $webhookData): bool
    {
        try {
            // Validate webhook signature
            $receivedSignature = request()->header('X-Callback-Token') ?? '';
            if (!$this->securityService->validateQrisWebhookSignature($webhookData, $receivedSignature)) {
                $this->securityService->logQrisSecurityEvent('invalid_webhook_signature', [
                    'webhook_data' => $webhookData,
                    'received_signature' => $receivedSignature,
                ]);
                return false;
            }
            
            // Find order by transaction ID
            $orderId = $webhookData['order_id'] ?? '';
            $order = Order::where('qris_transaction_id', $orderId)
                         ->orWhere('order_number', $orderId)
                         ->first();
            
            if (!$order) {
                $this->securityService->logQrisSecurityEvent('webhook_order_not_found', [
                    'order_id' => $orderId,
                    'webhook_data' => $webhookData,
                ]);
                return false;
            }
            
            // Process webhook based on status
            $transactionStatus = $webhookData['transaction_status'] ?? '';
            $this->processWebhookStatus($order, $transactionStatus, $webhookData);
            
            // Log successful webhook processing
            $this->securityService->logQrisSecurityEvent('webhook_processed_successfully', [
                'order_number' => $order->order_number,
                'transaction_status' => $transactionStatus,
                'webhook_data' => $webhookData,
            ]);
            
            return true;
            
        } catch (Exception $e) {
            $this->securityService->logQrisSecurityEvent('webhook_processing_failed', [
                'webhook_data' => $webhookData,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Cancel QRIS payment
     */
    public function cancelQrisPayment(string $qrisTransactionId): array
    {
        try {
            // Cancel payment via Midtrans
            $cancelResponse = $this->cancelMidtransQrisPayment($qrisTransactionId);
            
            // Update order status
            $order = Order::where('qris_transaction_id', $qrisTransactionId)->first();
            if ($order) {
                $order->update([
                    'payment_status' => 'cancelled',
                    'security_status' => 'verified',
                ]);
            }
            
            // Clear cache
            $this->clearPaymentCache($qrisTransactionId);
            
            $this->securityService->logQrisSecurityEvent('qris_payment_cancelled', [
                'qris_transaction_id' => $qrisTransactionId,
                'order_number' => $order->order_number ?? null,
            ]);
            
            return [
                'success' => true,
                'message' => 'QRIS payment cancelled successfully',
            ];
            
        } catch (Exception $e) {
            $this->securityService->logQrisSecurityEvent('qris_cancellation_failed', [
                'qris_transaction_id' => $qrisTransactionId,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Validate order for QRIS payment
     */
    private function validateOrder(Order $order): void
    {
        // Check order status
        if ($order->payment_status === 'paid') {
            throw new Exception('Order already paid');
        }
        
        // Check amount limits
        $amount = $order->total_amount;
        if ($amount < $this->config['min_amount']) {
            throw new Exception('Amount below minimum limit');
        }
        
        if ($amount > $this->config['max_amount']) {
            throw new Exception('Amount exceeds maximum limit');
        }
        
        // Validate payment amount integrity
        $expectedHash = $this->securityService->generatePaymentDataHash([
            'order_number' => $order->order_number,
            'amount' => $amount,
        ]);
        
        if ($order->payment_data_hash && !$this->securityService->validatePaymentDataIntegrity([
            'order_number' => $order->order_number,
            'amount' => $amount,
        ], $order->payment_data_hash)) {
            throw new Exception('Payment data integrity check failed');
        }
    }

    /**
     * Prepare payment data for QRIS
     */
    private function preparePaymentData(Order $order, string $qrisTransactionId): array
    {
        return [
            'order_id' => $qrisTransactionId,
            'amount' => (int) $order->total_amount,
            'currency' => $this->config['currency'],
            'merchant_id' => $this->config['merchant_id'],
            'terminal_id' => $this->config['terminal_id'],
            'store_id' => $this->config['store_id'],
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'customer_phone' => $order->customer_phone,
            'description' => "Payment for order {$order->order_number}",
            'expiry_duration' => $this->config['payment_timeout'],
        ];
    }

    /**
     * Create QRIS payment via Midtrans SnapBi
     */
    private function createMidtransQrisPayment(array $paymentData): array
    {
        try {
            $qrisBody = [
                'partnerReferenceNo' => $paymentData['order_id'],
                'amount' => [
                    'value' => number_format($paymentData['amount'], 2, '.', ''),
                    'currency' => $paymentData['currency'],
                ],
                'merchantId' => $paymentData['merchant_id'],
                'storeLabel' => $paymentData['store_id'],
                'terminalLabel' => $paymentData['terminal_id'],
                'validityPeriod' => now()->addSeconds($paymentData['expiry_duration'])->toISOString(),
                'additionalInfo' => [
                    'customerName' => $paymentData['customer_name'],
                    'customerEmail' => $paymentData['customer_email'],
                    'customerPhone' => $paymentData['customer_phone'],
                    'description' => $paymentData['description'],
                ],
            ];
            
            $response = SnapBi::qris()
                ->withBody($qrisBody)
                ->createPayment($paymentData['order_id']);
            
            if (!$response || !isset($response->responseCode) || $response->responseCode !== '2000000') {
                throw new Exception('Failed to create QRIS payment: ' . ($response->responseMessage ?? 'Unknown error'));
            }
            
            return [
                'qr_string' => $response->qrCode ?? null,
                'qr_code_url' => $response->qrCodeUrl ?? null,
                'reference_id' => $response->referenceNo ?? null,
                'expiry_time' => $response->validUntil ?? null,
                'response_code' => $response->responseCode,
                'response_message' => $response->responseMessage,
            ];
            
        } catch (Exception $e) {
            Log::error('Midtrans QRIS creation failed: ' . $e->getMessage());
            throw new Exception('Failed to create QRIS payment: ' . $e->getMessage());
        }
    }

    /**
     * Get QRIS status from Midtrans
     */
    private function getMidtransQrisStatus(string $qrisTransactionId): array
    {
        try {
            $statusBody = [
                'originalReferenceNo' => $qrisTransactionId,
                'serviceCode' => '47',
            ];
            
            $response = SnapBi::qris()
                ->withBody($statusBody)
                ->getStatus($qrisTransactionId);
            
            return [
                'transaction_status' => $response->transactionStatus ?? 'pending',
                'payment_type' => 'qris',
                'gross_amount' => $response->amount->value ?? 0,
                'transaction_time' => $response->transactionDate ?? null,
                'reference_id' => $response->referenceNo ?? null,
            ];
            
        } catch (Exception $e) {
            Log::error('Midtrans QRIS status check failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cancel QRIS payment via Midtrans
     */
    private function cancelMidtransQrisPayment(string $qrisTransactionId): array
    {
        try {
            $cancelBody = [
                'originalReferenceNo' => $qrisTransactionId,
                'reason' => 'Customer requested cancellation',
            ];
            
            $response = SnapBi::qris()
                ->withBody($cancelBody)
                ->cancel($qrisTransactionId);
            
            return [
                'success' => $response->responseCode === '2000000',
                'message' => $response->responseMessage ?? 'Cancelled',
            ];
            
        } catch (Exception $e) {
            Log::error('Midtrans QRIS cancellation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update order with QRIS data
     */
    private function updateOrderWithQrisData(Order $order, array $qrisResponse, string $qrisTransactionId, string $paymentSignature, array $fraudIndicators): void
    {
        $securityContext = request()->attributes->get('security_context', []);
        
        $order->update([
            'qris_transaction_id' => $qrisTransactionId,
            'payment_signature' => $paymentSignature,
            'payment_data' => array_merge($order->payment_data ?? [], $qrisResponse),
            'fraud_indicators' => $fraudIndicators,
            'is_suspicious' => !empty($fraudIndicators),
            'security_status' => empty($fraudIndicators) ? 'verified' : 'flagged',
            'client_ip' => $securityContext['client_ip'] ?? request()->ip(),
            'user_agent' => $securityContext['user_agent'] ?? request()->userAgent(),
            'session_id' => $securityContext['session_id'] ?? session()->getId(),
            'last_security_check' => now(),
        ]);
    }

    /**
     * Process webhook status
     */
    private function processWebhookStatus(Order $order, string $status, array $webhookData): void
    {
        switch ($status) {
            case 'settlement':
            case 'capture':
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                    'security_verified_at' => now(),
                    'verified_by' => 'qris_webhook',
                ]);
                break;
                
            case 'pending':
                $order->update(['payment_status' => 'pending']);
                break;
                
            case 'deny':
            case 'cancel':
            case 'expire':
                $order->update([
                    'payment_status' => 'failed',
                    'security_status' => 'verified',
                ]);
                break;
        }
    }

    /**
     * Cache payment data
     */
    private function cachePaymentData(string $qrisTransactionId, array $data): void
    {
        if ($this->config['cache']['enabled']) {
            $key = $this->config['cache']['prefix'] . 'payment_' . $qrisTransactionId;
            Cache::put($key, $data, $this->config['cache']['ttl']);
        }
    }

    /**
     * Cache payment status
     */
    private function cachePaymentStatus(string $qrisTransactionId, array $status): void
    {
        if ($this->config['cache']['enabled']) {
            $key = $this->config['cache']['prefix'] . 'status_' . $qrisTransactionId;
            Cache::put($key, $status, 60); // Cache status for 1 minute
        }
    }

    /**
     * Get cached payment status
     */
    private function getCachedPaymentStatus(string $qrisTransactionId): ?array
    {
        if ($this->config['cache']['enabled']) {
            $key = $this->config['cache']['prefix'] . 'status_' . $qrisTransactionId;
            return Cache::get($key);
        }
        
        return null;
    }

    /**
     * Clear payment cache
     */
    private function clearPaymentCache(string $qrisTransactionId): void
    {
        if ($this->config['cache']['enabled']) {
            $prefix = $this->config['cache']['prefix'];
            Cache::forget($prefix . 'payment_' . $qrisTransactionId);
            Cache::forget($prefix . 'status_' . $qrisTransactionId);
        }
    }
}
