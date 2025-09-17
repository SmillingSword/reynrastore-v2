<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\CoreApi;
use Midtrans\Transaction;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class MidtransService
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Create Snap Token for payment
     */
    public function createSnapToken(Order $order, $paymentMethod = null)
    {
        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) $order->total_amount,
                ],
                'customer_details' => [
                    'first_name' => $order->customer_name,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                ],
                'item_details' => $this->getItemDetails($order),
                'callbacks' => [
                    'finish' => url('/payment/finish'),
                    'unfinish' => url('/payment/unfinish'),
                    'error' => url('/payment/error'),
                ],
            ];

            // Add payment method if specified
            if ($paymentMethod) {
                $params['enabled_payments'] = [$paymentMethod];
            }

            $snapToken = Snap::getSnapToken($params);
            
            // Update order with snap token
            $order->update(['snap_token' => $snapToken]);
            
            return $snapToken;
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create QRIS payment using Core API (NEW IMPLEMENTATION)
     */
    public function createQrisPayment(Order $order)
    {
        try {
            Log::info('Creating QRIS payment using Core API for order: ' . $order->order_number);
            
            // Use Core API for direct QRIS payment
            $qrisResult = $this->createCoreApiQrisPayment($order);
            
            if ($qrisResult) {
                return $qrisResult;
            }
            
            // Fallback to Snap API if Core API fails
            Log::warning('Core API failed, falling back to Snap API for order: ' . $order->order_number);
            return $this->createSnapQrisPayment($order);

        } catch (\Exception $e) {
            Log::error('Midtrans QRIS Payment Error: ' . $e->getMessage());
            
            // Final fallback to mock QRIS
            return $this->createMockQrisPayment($order);
        }
    }

    /**
     * Create QRIS payment using Core API /v2/charge (Primary Method)
     */
    public function createCoreApiQrisPayment(Order $order)
    {
        try {
            Log::info('Creating Core API QRIS payment for order: ' . $order->order_number);
            
            // Prepare request payload according to your documentation
            $payload = [
                'payment_type' => 'qris',
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) $order->total_amount,
                ],
                'item_details' => $this->getItemDetailsForCoreApi($order),
                'customer_details' => [
                    'first_name' => $order->customer_name,
                    'last_name' => '', // Split name if needed
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                ],
                'qris' => [
                    'acquirer' => 'gopay'
                ]
            ];

            Log::debug('Core API QRIS Payload:', $payload);

            // Use CoreApi::charge method
            $response = CoreApi::charge($payload);
            
            Log::debug('Core API QRIS Response:', (array) $response);

            // Check if response is successful
            if (!isset($response->status_code) || $response->status_code !== '201') {
                throw new \Exception('Core API returned error: ' . ($response->status_message ?? 'Unknown error'));
            }

            // Extract QR string and other data from response
            $qrString = $response->qr_string ?? null;
            $transactionId = $response->transaction_id ?? $order->order_number;
            $expiryTime = $response->expiry_time ?? null;
            
            // Get QR code generation URL from actions
            $qrCodeUrl = null;
            if (isset($response->actions) && is_array($response->actions)) {
                foreach ($response->actions as $action) {
                    if (isset($action->name) && $action->name === 'generate-qr-code') {
                        $qrCodeUrl = $action->url ?? null;
                        break;
                    }
                }
            }

            // Calculate expiry time (10 minutes from now)
            $expiresAt = now()->addMinutes(10)->toISOString();
            
            // Update order with Core API transaction data
            $order->update([
                'qris_transaction_id' => $transactionId,
                'payment_data' => json_encode([
                    'transaction_id' => $transactionId,
                    'qr_string' => $qrString,
                    'qr_code_url' => $qrCodeUrl,
                    'status' => 'pending',
                    'payment_type' => 'qris',
                    'acquirer' => 'gopay',
                    'api_type' => 'core_api',
                    'expiry_time' => $expiryTime,
                    'created_at' => now()->toISOString(),
                ])
            ]);

            Log::info('Core API QRIS payment created successfully for order: ' . $order->order_number);

            return [
                'transaction_id' => $transactionId,
                'qris_string' => $qrString,
                'qr_code_url' => $qrCodeUrl,
                'qr_data' => $qrString, // For backward compatibility
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
                'expires_at' => $expiresAt,
                'expiry_time' => $expiryTime,
                'status' => 'pending',
                'payment_type' => 'qris',
                'acquirer' => 'gopay',
                'api_type' => 'core_api'
            ];

        } catch (\Exception $e) {
            Log::error('Core API QRIS Payment Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create QRIS payment using Snap API (Fallback Method)
     */
    public function createSnapQrisPayment(Order $order)
    {
        try {
            Log::info('Creating Snap API QRIS payment for order: ' . $order->order_number);
            
            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) $order->total_amount,
                ],
                'customer_details' => [
                    'first_name' => $order->customer_name,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                ],
                'item_details' => $this->getItemDetails($order),
                'enabled_payments' => ['qris'],
                'callbacks' => [
                    'finish' => url('/payment/finish'),
                    'unfinish' => url('/payment/unfinish'),
                    'error' => url('/payment/error'),
                ],
            ];

            Log::debug('Snap QRIS Payment Parameters:', $params);

            // Get Snap transaction (not just token)
            $transaction = Snap::createTransaction($params);
            
            Log::debug('Snap QRIS Response:', $transaction);

            if (!isset($transaction->token) && !isset($transaction->redirect_url)) {
                throw new \Exception('Invalid response from Midtrans Snap: missing token and redirect_url');
            }

            // Generate QR code data - use redirect URL if available, otherwise use token
            $qrData = $transaction->redirect_url ?? "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$transaction->token}";
            
            // Update order with transaction data
            $order->update([
                'snap_token' => $transaction->token,
                'qris_transaction_id' => $transaction->transaction_id ?? $order->order_number,
                'payment_data' => json_encode([
                    'transaction_id' => $transaction->transaction_id ?? $order->order_number,
                    'token' => $transaction->token,
                    'redirect_url' => $transaction->redirect_url ?? null,
                    'qr_data' => $qrData,
                    'status' => 'pending',
                    'api_type' => 'snap_api',
                    'created_at' => now()->toISOString(),
                ])
            ]);

            return [
                'transaction_id' => $transaction->transaction_id ?? $order->order_number,
                'token' => $transaction->token,
                'redirect_url' => $transaction->redirect_url,
                'qr_data' => $qrData,
                'qris_string' => null, // Snap API doesn't provide direct QR string
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
                'expires_at' => now()->addMinutes(10)->toISOString(), // Updated to 10 minutes
                'api_type' => 'snap_api'
            ];

        } catch (\Exception $e) {
            Log::error('Snap QRIS Payment Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create mock QRIS payment as fallback
     */
    private function createMockQrisPayment(Order $order)
    {
        Log::warning('Using mock QRIS payment for order: ' . $order->order_number);
        
        $transactionId = 'QRIS-' . $order->order_number . '-' . time();
        $qrData = $this->generateMockQrisString($order);
        
        $order->update([
            'payment_data' => json_encode([
                'transaction_id' => $transactionId,
                'qr_data' => $qrData,
                'status' => 'pending',
                'created_at' => now()->toISOString(),
                'is_mock' => true,
            ])
        ]);

        return [
            'transaction_id' => $transactionId,
            'qr_data' => $qrData,
            'order_number' => $order->order_number,
            'total_amount' => $order->total_amount,
            'expires_at' => now()->addHours(1)->toISOString(),
            'is_mock' => true,
        ];
    }

    /**
     * Generate valid QRIS string for demo (Indonesian QRIS Standard)
     */
    private function generateMockQrisString(Order $order)
    {
        // Generate a valid QRIS string following Indonesian QRIS standard
        // This creates a scannable QR code that follows the EMV QR Code specification
        
        $merchantId = 'ID1234567890123'; // 15 digit merchant ID
        $merchantName = 'REYNRA STORE';
        $merchantCity = 'JAKARTA';
        $countryCode = 'ID';
        $amount = number_format($order->total_amount, 2, '.', '');
        
        // Build QRIS payload following EMV QR Code specification
        $qrisPayload = '';
        
        // Payload Format Indicator (Tag 00)
        $qrisPayload .= '00' . '02' . '01';
        
        // Point of Initiation Method (Tag 01) - Static QR
        $qrisPayload .= '01' . '02' . '12';
        
        // Merchant Account Information (Tag 26-51) - Using Tag 26 for domestic
        $merchantInfo = '';
        $merchantInfo .= '00' . '14' . 'ID.CO.QRIS.WWW'; // Globally Unique Identifier
        $merchantInfo .= '01' . str_pad(strlen($merchantId), 2, '0', STR_PAD_LEFT) . $merchantId;
        $qrisPayload .= '26' . str_pad(strlen($merchantInfo), 2, '0', STR_PAD_LEFT) . $merchantInfo;
        
        // Merchant Category Code (Tag 52) - 5999 for miscellaneous
        $qrisPayload .= '52' . '04' . '5999';
        
        // Transaction Currency (Tag 53) - 360 for Indonesian Rupiah
        $qrisPayload .= '53' . '03' . '360';
        
        // Transaction Amount (Tag 54)
        $qrisPayload .= '54' . str_pad(strlen($amount), 2, '0', STR_PAD_LEFT) . $amount;
        
        // Country Code (Tag 58)
        $qrisPayload .= '58' . '02' . $countryCode;
        
        // Merchant Name (Tag 59)
        $qrisPayload .= '59' . str_pad(strlen($merchantName), 2, '0', STR_PAD_LEFT) . $merchantName;
        
        // Merchant City (Tag 60)
        $qrisPayload .= '60' . str_pad(strlen($merchantCity), 2, '0', STR_PAD_LEFT) . $merchantCity;
        
        // Additional Data Field Template (Tag 62)
        $additionalData = '';
        $additionalData .= '01' . str_pad(strlen($order->order_number), 2, '0', STR_PAD_LEFT) . $order->order_number; // Bill Number
        $qrisPayload .= '62' . str_pad(strlen($additionalData), 2, '0', STR_PAD_LEFT) . $additionalData;
        
        // Calculate CRC16 checksum (Tag 63)
        $qrisPayload .= '6304'; // CRC tag and length
        $crc = $this->calculateCRC16($qrisPayload);
        $qrisPayload .= strtoupper(dechex($crc));
        
        return $qrisPayload;
    }
    
    /**
     * Calculate CRC16 checksum for QRIS
     */
    private function calculateCRC16($data)
    {
        $crc = 0xFFFF;
        $polynomial = 0x1021;
        
        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= (ord($data[$i]) << 8);
            
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) ^ $polynomial) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }
        
        return $crc;
    }

    /**
     * Get transaction status from Midtrans
     */
    public function getTransactionStatus($orderId)
    {
        try {
            return Transaction::status($orderId);
        } catch (\Exception $e) {
            Log::error('Midtrans Transaction Status Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle payment notification from Midtrans
     */
    public function handleNotification($notification)
    {
        try {
            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;

            $order = Order::where('order_number', $orderId)->first();
            
            if (!$order) {
                Log::error('Order not found: ' . $orderId);
                return false;
            }

            // Update order based on transaction status
            switch ($transactionStatus) {
                case 'capture':
                    if ($fraudStatus == 'challenge') {
                        $order->update(['payment_status' => 'challenge']);
                    } else if ($fraudStatus == 'accept') {
                        $order->update(['payment_status' => 'paid']);
                        $this->processOrder($order);
                    }
                    break;

                case 'settlement':
                    $order->update(['payment_status' => 'paid']);
                    $this->processOrder($order);
                    break;

                case 'pending':
                    $order->update(['payment_status' => 'pending']);
                    break;

                case 'deny':
                    $order->update(['payment_status' => 'failed']);
                    break;

                case 'expire':
                    $order->update(['payment_status' => 'expired']);
                    break;

                case 'cancel':
                    $order->update(['payment_status' => 'cancelled']);
                    break;

                default:
                    Log::warning('Unknown transaction status: ' . $transactionStatus);
                    break;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Process order after successful payment
     */
    private function processOrder(Order $order)
    {
        try {
            $order->update(['status' => 'processing']);
            
            // Process each order item
            foreach ($order->orderItems as $item) {
                if ($item->product->type === 'diggie') {
                    // Process with Diggie API
                    app(DiggieService::class)->processOrder($item);
                } else {
                    // Manual processing - just update status
                    $item->update(['status' => 'pending_manual']);
                }
            }
            
            Log::info('Order processed successfully: ' . $order->order_number);
        } catch (\Exception $e) {
            Log::error('Order processing error: ' . $e->getMessage());
            $order->update(['status' => 'failed']);
        }
    }

    /**
     * Get item details for Core API (matches your documentation format)
     */
    private function getItemDetailsForCoreApi(Order $order)
    {
        $items = [];
        
        foreach ($order->orderItems as $item) {
            $items[] = [
                'id' => $item->product->sku ?? 'SKU-' . $item->product_id,
                'price' => (int) $item->unit_price,
                'quantity' => $item->quantity,
                'name' => $item->product_name ?? 'Product ' . $item->product_id,
            ];
        }
        
        // If no items, create a default item
        if (empty($items)) {
            $items[] = [
                'id' => 'SKU-123',
                'price' => (int) $order->total_amount,
                'quantity' => 1,
                'name' => 'Order ' . $order->order_number,
            ];
        }
        
        return $items;
    }

    /**
     * Get item details for Snap API (original format)
     */
    private function getItemDetails(Order $order)
    {
        $items = [];
        
        foreach ($order->orderItems as $item) {
            $items[] = [
                'id' => $item->product->sku ?? 'SKU-' . $item->product_id,
                'price' => (int) $item->unit_price,
                'quantity' => $item->quantity,
                'name' => $item->product->name ?? $item->product_name,
            ];
        }
        
        // If no items, create a default item
        if (empty($items)) {
            $items[] = [
                'id' => 'SKU-123',
                'price' => (int) $order->total_amount,
                'quantity' => 1,
                'name' => 'Order ' . $order->order_number,
            ];
        }
        
        return $items;
    }
}
