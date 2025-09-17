<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\DiggieService;

echo "Testing DigiFlazz Connection...\n";
echo "================================\n\n";

try {
    $service = app(DiggieService::class);
    
    echo "1. Testing Balance API...\n";
    $balance = $service->getBalance();
    echo "Balance Response: " . json_encode($balance, JSON_PRETTY_PRINT) . "\n\n";
    
    echo "2. Testing Product List API...\n";
    $products = $service->getProducts();
    echo "Products Count: " . count($products) . "\n";
    
    if (count($products) > 0) {
        echo "First Product Sample: " . json_encode($products[0], JSON_PRETTY_PRINT) . "\n";
    }
    
    echo "\n✅ DigiFlazz connection successful!\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Check logs for more details.\n";
}
