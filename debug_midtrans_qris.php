<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING MIDTRANS QRIS ISSUE ===\n\n";

// Check environment variables
echo "1. CHECKING MIDTRANS CONFIGURATION\n";
echo "===================================\n";
echo "Server Key: " . (env('MIDTRANS_SERVER_KEY') ? 'SET (' . substr(env('MIDTRANS_SERVER_KEY'), 0, 10) . '...)' : 'NOT SET') . "\n";
echo "Client Key: " . (env('MIDTRANS_CLIENT_KEY') ? 'SET (' . substr(env('MIDTRANS_CLIENT_KEY'), 0, 10) . '...)' : 'NOT SET') . "\n";
echo "Is Production: " . (env('MIDTRANS_IS_PRODUCTION', false) ? 'TRUE' : 'FALSE') . "\n";
echo "Is Sanitized: " . (env('MIDTRANS_IS_SANITIZED', true) ? 'TRUE' : 'FALSE') . "\n";
echo "Is 3DS: " . (env('MIDTRANS_IS_3DS', true) ? 'TRUE' : 'FALSE') . "\n\n";

// Test Midtrans configuration
try {
    echo "2. TESTING MIDTRANS CONNECTION\n";
    echo "==============================\n";
    
    \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
    \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
    \Midtrans\Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
    \Midtrans\Config::$is3ds = env('MIDTRANS_IS_3DS', true);
    
    echo "✅ Midtrans Config loaded successfully\n\n";
    
    // Test simple QRIS transaction
    echo "3. TESTING QRIS TRANSACTION CREATION\n";
    echo "====================================\n";
    
    $testOrderId = 'TEST-QRIS-' . time();
    $params = [
        'transaction_details' => [
            'order_id' => $testOrderId,
            'gross_amount' => 10000,
        ],
        'customer_details' => [
            'first_name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '081234567890',
        ],
        'item_details' => [
            [
                'id' => 'test-item',
                'price' => 10000,
                'quantity' => 1,
                'name' => 'Test Item',
            ]
        ],
        'enabled_payments' => ['qris'],
    ];
    
    echo "Request Parameters:\n";
    echo json_encode($params, JSON_PRETTY_PRINT) . "\n\n";
    
    echo "Calling Midtrans Snap::createTransaction()...\n";
    $result = \Midtrans\Snap::createTransaction($params);
    
    echo "✅ SUCCESS! QRIS Transaction created\n";
    echo "Response:\n";
    echo "- Token: " . ($result->token ?? 'N/A') . "\n";
    echo "- Redirect URL: " . ($result->redirect_url ?? 'N/A') . "\n";
    echo "- Transaction ID: " . ($result->transaction_id ?? 'N/A') . "\n\n";
    
    echo "Full Response:\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
    
    echo "🎉 MIDTRANS QRIS IS WORKING CORRECTLY!\n";
    echo "The issue might be in the PaymentController implementation.\n\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n";
    echo "This explains why mock QRIS is being used instead!\n\n";
    
    echo "Common issues:\n";
    echo "1. Invalid Server Key\n";
    echo "2. Network connectivity issues\n";
    echo "3. Midtrans service temporarily down\n";
    echo "4. Invalid request parameters\n\n";
}

// Check recent orders to see if they're using mock QRIS
echo "4. CHECKING RECENT ORDERS FOR MOCK QRIS\n";
echo "=======================================\n";

try {
    $recentOrders = \App\Models\Order::where('payment_method', 'qris')
        ->whereNotNull('payment_data')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    foreach ($recentOrders as $order) {
        $paymentData = is_string($order->payment_data) 
            ? json_decode($order->payment_data, true) 
            : $order->payment_data;
        
        $isMock = isset($paymentData['is_mock']) && $paymentData['is_mock'];
        
        echo "Order: {$order->order_number}\n";
        echo "- Is Mock: " . ($isMock ? 'YES' : 'NO') . "\n";
        echo "- Transaction ID: " . ($paymentData['transaction_id'] ?? 'N/A') . "\n";
        echo "- Has QR Data: " . (isset($paymentData['qr_data']) ? 'YES' : 'NO') . "\n";
        echo "- Created: {$order->created_at}\n\n";
    }
    
} catch (Exception $e) {
    echo "Error checking orders: " . $e->getMessage() . "\n";
}

echo "=== DEBUG COMPLETE ===\n";
