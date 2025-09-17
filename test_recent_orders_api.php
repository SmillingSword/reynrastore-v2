<?php

// Test script untuk API recent orders
require_once 'vendor/autoload.php';

// Konfigurasi
$baseUrl = 'http://localhost:8000'; // Sesuaikan dengan URL aplikasi Anda
$apiUrl = $baseUrl . '/api/v1/orders/recent';

echo "Testing Recent Orders API Endpoint\n";
echo "==================================\n\n";

// Test API endpoint
echo "1. Testing API: GET {$apiUrl}\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status Code: {$httpCode}\n";

if ($response) {
    $data = json_decode($response, true);
    
    if ($data) {
        echo "Response Success: " . ($data['success'] ? 'true' : 'false') . "\n";
        
        if (isset($data['data']) && is_array($data['data'])) {
            echo "Number of orders returned: " . count($data['data']) . "\n\n";
            
            // Tampilkan beberapa order pertama
            foreach (array_slice($data['data'], 0, 3) as $index => $order) {
                echo "Order " . ($index + 1) . ":\n";
                echo "  - Order Number: " . ($order['order_number'] ?? 'N/A') . "\n";
                echo "  - Customer: " . ($order['customer_name'] ?? 'N/A') . "\n";
                echo "  - Total: Rp " . number_format($order['total_amount'] ?? 0, 0, ',', '.') . "\n";
                echo "  - Status: " . ($order['status'] ?? 'N/A') . "\n";
                echo "  - Payment Status: " . ($order['payment_status'] ?? 'N/A') . "\n";
                
                if (isset($order['orderItems']) && is_array($order['orderItems'])) {
                    echo "  - Items: " . count($order['orderItems']) . "\n";
                    foreach ($order['orderItems'] as $item) {
                        echo "    * " . ($item['product']['name'] ?? 'Unknown Product') . "\n";
                    }
                }
                echo "\n";
            }
        } else {
            echo "No orders data found in response\n";
        }
        
        // Tampilkan raw response untuk debugging
        echo "Raw Response:\n";
        echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
        
    } else {
        echo "Failed to decode JSON response\n";
        echo "Raw response: " . $response . "\n";
    }
} else {
    echo "Failed to get response from API\n";
}

echo "\n==================================\n";
echo "Test completed!\n";
