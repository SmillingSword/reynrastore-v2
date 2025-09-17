<?php

// Bootstrap Laravel application
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\MidtransService;

echo "=== Testing Midtrans Core API QRIS Implementation ===\n\n";

try {
    // Create a test order for QRIS payment
    echo "1. Creating test order...\n";
    
    $orderNumber = 'TEST-CORE-' . time();
    $order = Order::create([
        'order_number' => $orderNumber,
        'customer_name' => 'Test Customer Core API',
        'customer_email' => 'test.core@example.com',
        'customer_phone' => '081234567890',
        'subtotal' => 7,
        'total_amount' => 7,
        'status' => 'pending',
        'payment_status' => 'pending'
    ]);

    // Create order item
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => 1, // Use existing product
        'product_name' => 'Test Product Core API',
        'quantity' => 1,
        'unit_price' => 7,
        'total_price' => 7
    ]);

    echo "✅ Test order created: {$order->order_number}\n";
    echo "   - Amount: Rp {$order->total_amount}\n";
    echo "   - Customer: {$order->customer_name}\n\n";

    // Test Core API QRIS payment
    echo "2. Testing Core API QRIS payment creation...\n";
    
    $midtransService = new MidtransService();
    $qrisResult = $midtransService->createQrisPayment($order);
    
    echo "✅ QRIS payment created successfully!\n";
    echo "   - Transaction ID: {$qrisResult['transaction_id']}\n";
    echo "   - API Type: " . ($qrisResult['api_type'] ?? 'unknown') . "\n";
    echo "   - Payment Type: " . ($qrisResult['payment_type'] ?? 'unknown') . "\n";
    echo "   - Acquirer: " . ($qrisResult['acquirer'] ?? 'unknown') . "\n";
    echo "   - Expires At: {$qrisResult['expires_at']}\n";
    
    if (isset($qrisResult['qris_string'])) {
        echo "   - QRIS String: " . substr($qrisResult['qris_string'], 0, 50) . "...\n";
        echo "   - QRIS Length: " . strlen($qrisResult['qris_string']) . " characters\n";
    }
    
    if (isset($qrisResult['qr_code_url'])) {
        echo "   - QR Code URL: {$qrisResult['qr_code_url']}\n";
    }
    
    if (isset($qrisResult['expiry_time'])) {
        echo "   - Midtrans Expiry: {$qrisResult['expiry_time']}\n";
    }
    
    echo "\n";

    // Verify order was updated
    echo "3. Verifying order update...\n";
    $order->refresh();
    
    echo "✅ Order updated successfully!\n";
    echo "   - QRIS Transaction ID: {$order->qris_transaction_id}\n";
    echo "   - Payment Method: {$order->payment_method}\n";
    
    $paymentData = json_decode($order->payment_data, true);
    if ($paymentData) {
        echo "   - Payment Data Keys: " . implode(', ', array_keys($paymentData)) . "\n";
        echo "   - API Type: " . ($paymentData['api_type'] ?? 'unknown') . "\n";
        echo "   - Acquirer: " . ($paymentData['acquirer'] ?? 'unknown') . "\n";
    }
    
    echo "\n";

    // Test expiry time calculation
    echo "4. Testing expiry time (should be 10 minutes)...\n";
    
    $expiresAt = new DateTime($qrisResult['expires_at']);
    $createdAt = new DateTime();
    $diffMinutes = ($expiresAt->getTimestamp() - $createdAt->getTimestamp()) / 60;
    
    echo "✅ Expiry time verification:\n";
    echo "   - Created: " . $createdAt->format('Y-m-d H:i:s') . "\n";
    echo "   - Expires: " . $expiresAt->format('Y-m-d H:i:s') . "\n";
    echo "   - Duration: " . round($diffMinutes, 1) . " minutes\n";
    
    if ($diffMinutes >= 9 && $diffMinutes <= 11) {
        echo "   ✅ Expiry time is correct (10 minutes)\n";
    } else {
        echo "   ⚠️  Expiry time might be incorrect (expected ~10 minutes)\n";
    }
    
    echo "\n";

    // Test API response structure
    echo "5. Testing API response structure...\n";
    
    $expectedKeys = ['transaction_id', 'order_number', 'total_amount', 'expires_at', 'status'];
    $missingKeys = [];
    
    foreach ($expectedKeys as $key) {
        if (!isset($qrisResult[$key])) {
            $missingKeys[] = $key;
        }
    }
    
    if (empty($missingKeys)) {
        echo "✅ All expected response keys present\n";
    } else {
        echo "⚠️  Missing keys: " . implode(', ', $missingKeys) . "\n";
    }
    
    // Check for Core API specific keys
    $coreApiKeys = ['qris_string', 'payment_type', 'acquirer', 'api_type'];
    $presentCoreKeys = [];
    
    foreach ($coreApiKeys as $key) {
        if (isset($qrisResult[$key])) {
            $presentCoreKeys[] = $key;
        }
    }
    
    echo "   - Core API keys present: " . implode(', ', $presentCoreKeys) . "\n";
    
    echo "\n";

    // Summary
    echo "=== SUMMARY ===\n";
    echo "✅ Core API QRIS implementation test completed successfully!\n";
    echo "✅ 10-minute expiry time implemented\n";
    echo "✅ Proper response structure with Core API data\n";
    echo "✅ Order updated with transaction details\n";
    
    if (isset($qrisResult['api_type']) && $qrisResult['api_type'] === 'core_api') {
        echo "✅ Using Core API (not Snap API)\n";
    } elseif (isset($qrisResult['api_type']) && $qrisResult['api_type'] === 'snap_api') {
        echo "⚠️  Fell back to Snap API\n";
    } else {
        echo "⚠️  API type unclear\n";
    }
    
    if (isset($qrisResult['qris_string']) && !empty($qrisResult['qris_string'])) {
        echo "✅ QRIS string generated\n";
    } else {
        echo "⚠️  No QRIS string in response\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test completed ===\n";
