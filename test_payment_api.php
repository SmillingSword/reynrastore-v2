<?php

require_once 'vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING PAYMENT API ===\n\n";

try {
    // Test 1: Create a fresh test order for payment
    echo "1. Creating fresh test order for payment...\n";
    $orderNumber = 'ORD-TEST-' . time();
    
    $order = App\Models\Order::create([
        'order_number' => $orderNumber,
        'customer_name' => 'Test Customer QRIS',
        'customer_email' => 'test.qris@example.com',
        'customer_phone' => '081234567890',
        'subtotal' => 25000,
        'total_amount' => 25000,
        'status' => 'pending',
        'payment_method' => null,
        'payment_status' => 'pending'
    ]);
    echo "Test order created with ID: {$order->id}, Order Number: {$orderNumber}\n";
    
    echo "\n2. Testing PaymentController createPayment method...\n";
    
    // Create controller instance with required dependencies
    $midtransService = app(App\Services\MidtransService::class);
    $qrisService = app(App\Services\QrisService::class);
    $securityService = app(App\Services\EnhancedSecurityService::class);
    $controller = new App\Http\Controllers\Api\PaymentController($midtransService, $qrisService, $securityService);
    
    // Create request
    $request = new Illuminate\Http\Request();
    $request->merge([
        'order_number' => $orderNumber,
        'payment_method' => 'qris'
    ]);
    
    // Test the method
    $response = $controller->createPayment($request);
    
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
