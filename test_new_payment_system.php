<?php

require_once 'vendor/autoload.php';

// Test new payment system
function testNewPaymentSystem() {
    $baseUrl = 'http://127.0.0.1:8000/api/v1';
    
    echo "=== Testing New Payment System ===\n\n";
    
    // Test 1: Create QRIS Payment
    echo "1. Testing QRIS Payment Creation...\n";
    $qrisResponse = makeRequest('POST', "$baseUrl/payment/create", [
        'order_number' => 'RS20250822HASTGS',
        'payment_method' => 'qris'
    ]);
    
    if ($qrisResponse && isset($qrisResponse['success']) && $qrisResponse['success']) {
        echo "✅ QRIS Payment created successfully\n";
        echo "   Payment Type: " . ($qrisResponse['data']['payment_type'] ?? 'N/A') . "\n";
        echo "   Transaction ID: " . ($qrisResponse['data']['transaction_id'] ?? 'N/A') . "\n";
        if (isset($qrisResponse['data']['qris_string'])) {
            echo "   QRIS String: " . substr($qrisResponse['data']['qris_string'], 0, 50) . "...\n";
        }
    } else {
        echo "❌ QRIS Payment creation failed\n";
        echo "   Error: " . ($qrisResponse['message'] ?? 'Unknown error') . "\n";
    }
    echo "\n";
    
    // Test 2: Create Bank Transfer Payment
    echo "2. Testing Bank Transfer Payment Creation...\n";
    $bankResponse = makeRequest('POST', "$baseUrl/payment/create", [
        'order_number' => 'RS20250822HASTGS',
        'payment_method' => 'bca'
    ]);
    
    if ($bankResponse && isset($bankResponse['success']) && $bankResponse['success']) {
        echo "✅ Bank Transfer Payment created successfully\n";
        echo "   Payment Type: " . ($bankResponse['data']['payment_type'] ?? 'N/A') . "\n";
        echo "   Bank Name: " . ($bankResponse['data']['bank_name'] ?? 'N/A') . "\n";
        echo "   Account Number: " . ($bankResponse['data']['account_number'] ?? 'N/A') . "\n";
        echo "   Account Name: " . ($bankResponse['data']['account_name'] ?? 'N/A') . "\n";
        echo "   Unique Amount: " . ($bankResponse['data']['amount'] ?? 'N/A') . "\n";
    } else {
        echo "❌ Bank Transfer Payment creation failed\n";
        echo "   Error: " . ($bankResponse['message'] ?? 'Unknown error') . "\n";
    }
    echo "\n";
    
    // Test 3: Create E-Wallet Payment
    echo "3. Testing E-Wallet Payment Creation...\n";
    $ewalletResponse = makeRequest('POST', "$baseUrl/payment/create", [
        'order_number' => 'RS20250822HASTGS',
        'payment_method' => 'dana'
    ]);
    
    if ($ewalletResponse && isset($ewalletResponse['success']) && $ewalletResponse['success']) {
        echo "✅ E-Wallet Payment created successfully\n";
        echo "   Payment Type: " . ($ewalletResponse['data']['payment_type'] ?? 'N/A') . "\n";
        echo "   Wallet Name: " . ($ewalletResponse['data']['wallet_name'] ?? 'N/A') . "\n";
        echo "   Phone Number: " . ($ewalletResponse['data']['phone_number'] ?? 'N/A') . "\n";
        echo "   Account Name: " . ($ewalletResponse['data']['account_name'] ?? 'N/A') . "\n";
        echo "   Unique Amount: " . ($ewalletResponse['data']['amount'] ?? 'N/A') . "\n";
    } else {
        echo "❌ E-Wallet Payment creation failed\n";
        echo "   Error: " . ($ewalletResponse['message'] ?? 'Unknown error') . "\n";
    }
    echo "\n";
    
    // Test 4: Get Order Payment Info
    echo "4. Testing Get Order Payment Info...\n";
    $orderResponse = makeRequest('GET', "$baseUrl/orders/RS20250822HASTGS/payment");
    
    if ($orderResponse && isset($orderResponse['success']) && $orderResponse['success']) {
        echo "✅ Order Payment Info retrieved successfully\n";
        echo "   Order Number: " . ($orderResponse['data']['order']['order_number'] ?? 'N/A') . "\n";
        echo "   Payment Method: " . ($orderResponse['data']['order']['payment_method'] ?? 'N/A') . "\n";
        echo "   Total Amount: " . ($orderResponse['data']['order']['total_amount'] ?? 'N/A') . "\n";
        echo "   Unique Amount: " . ($orderResponse['data']['order']['unique_amount'] ?? 'N/A') . "\n";
        echo "   Payment Status: " . ($orderResponse['data']['order']['payment_status'] ?? 'N/A') . "\n";
    } else {
        echo "❌ Get Order Payment Info failed\n";
        echo "   Error: " . ($orderResponse['message'] ?? 'Unknown error') . "\n";
    }
    echo "\n";
    
    echo "=== Payment System Test Complete ===\n";
}

function makeRequest($method, $url, $data = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($response === false) {
        return null;
    }
    
    $decoded = json_decode($response, true);
    
    if ($httpCode >= 400) {
        echo "HTTP Error $httpCode: $response\n";
    }
    
    return $decoded;
}

// Run the test
testNewPaymentSystem();
