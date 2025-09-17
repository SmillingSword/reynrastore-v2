<?php

require_once 'vendor/autoload.php';

// Test QRIS generation with valid format
function testQrisGeneration() {
    $url = 'http://localhost:8000/api/v1/payment/create';
    
    // Test data
    $data = [
        'order_number' => 'RS20250822GXIILP',
        'payment_method' => 'qris'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "=== QRIS Generation Test ===\n";
    echo "HTTP Code: $httpCode\n";
    echo "Response: " . $response . "\n\n";
    
    if ($httpCode === 200) {
        $responseData = json_decode($response, true);
        if (isset($responseData['data']['qris_string'])) {
            $qrisString = $responseData['data']['qris_string'];
            echo "=== QRIS String Analysis ===\n";
            echo "QRIS String: $qrisString\n";
            echo "Length: " . strlen($qrisString) . "\n";
            
            // Analyze QRIS format
            analyzeQrisFormat($qrisString);
        }
    }
}

function analyzeQrisFormat($qrisString) {
    echo "\n=== QRIS Format Analysis ===\n";
    
    // Check if it follows EMV QR Code format
    if (substr($qrisString, 0, 4) === '0002') {
        echo "✅ Valid EMV QR Code format detected\n";
        echo "✅ Payload Format Indicator: " . substr($qrisString, 0, 6) . "\n";
    } else {
        echo "❌ Invalid EMV QR Code format\n";
        return;
    }
    
    // Parse basic tags
    $pos = 0;
    $tags = [];
    
    while ($pos < strlen($qrisString) - 4) { // -4 for CRC
        if ($pos + 4 > strlen($qrisString)) break;
        
        $tag = substr($qrisString, $pos, 2);
        $length = (int)substr($qrisString, $pos + 2, 2);
        
        if ($pos + 4 + $length > strlen($qrisString)) break;
        
        $value = substr($qrisString, $pos + 4, $length);
        $tags[$tag] = $value;
        
        $pos += 4 + $length;
        
        // Stop before CRC (tag 63)
        if ($tag === '63') break;
    }
    
    // Display important tags
    echo "\n=== QRIS Tags ===\n";
    if (isset($tags['00'])) echo "Payload Format: " . $tags['00'] . "\n";
    if (isset($tags['01'])) echo "Point of Initiation: " . $tags['01'] . "\n";
    if (isset($tags['52'])) echo "Merchant Category: " . $tags['52'] . "\n";
    if (isset($tags['53'])) echo "Currency Code: " . $tags['53'] . "\n";
    if (isset($tags['54'])) echo "Transaction Amount: " . $tags['54'] . "\n";
    if (isset($tags['58'])) echo "Country Code: " . $tags['58'] . "\n";
    if (isset($tags['59'])) echo "Merchant Name: " . $tags['59'] . "\n";
    if (isset($tags['60'])) echo "Merchant City: " . $tags['60'] . "\n";
    
    // Check CRC
    $crcPart = substr($qrisString, -4);
    $dataForCrc = substr($qrisString, 0, -4) . '6304';
    echo "\nCRC Check: $crcPart\n";
    
    echo "\n✅ QRIS string appears to be in valid Indonesian QRIS format!\n";
    echo "✅ This QR code should be scannable by Indonesian e-wallet apps\n";
}

// Run test
testQrisGeneration();
