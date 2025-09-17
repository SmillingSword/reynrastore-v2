<?php

// Test API endpoint untuk orders
$url = 'http://localhost:8000/api/v1/test/orders';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if ($data && isset($data['data'])) {
        echo "Total orders found: " . count($data['data']) . "\n";
        foreach ($data['data'] as $order) {
            echo "Order #" . $order['id'] . " - " . $order['customer_name'] . " - " . $order['status'] . "\n";
        }
    }
} else {
    echo "Error: " . $response . "\n";
}
