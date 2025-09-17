<?php

return [
    /*
    |--------------------------------------------------------------------------
    | QRIS Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for QRIS payment integration with Midtrans
    |
    */

    'merchant_id' => env('QRIS_MERCHANT_ID', 'M001234'),
    
    'terminal_id' => env('QRIS_TERMINAL_ID', 'T001'),
    
    'store_id' => env('QRIS_STORE_ID', 'S001'),
    
    /*
    |--------------------------------------------------------------------------
    | QRIS Security Settings
    |--------------------------------------------------------------------------
    */
    
    'signature_key' => env('QRIS_SIGNATURE_KEY', env('APP_KEY')),
    
    'encryption_key' => env('QRIS_ENCRYPTION_KEY', env('APP_KEY')),
    
    /*
    |--------------------------------------------------------------------------
    | QRIS Payment Settings
    |--------------------------------------------------------------------------
    */
    
    'currency' => 'IDR',
    
    'country_code' => 'ID',
    
    'payment_timeout' => env('QRIS_PAYMENT_TIMEOUT', 900), // 15 minutes in seconds
    
    'max_amount' => env('QRIS_MAX_AMOUNT', 50000000), // 50 million IDR
    
    'min_amount' => env('QRIS_MIN_AMOUNT', 1000), // 1000 IDR
    
    /*
    |--------------------------------------------------------------------------
    | QRIS Webhook Settings
    |--------------------------------------------------------------------------
    */
    
    'webhook_url' => env('QRIS_WEBHOOK_URL', env('APP_URL') . '/api/v1/payment/qris/webhook'),
    
    'webhook_secret' => env('QRIS_WEBHOOK_SECRET'),
    
    /*
    |--------------------------------------------------------------------------
    | QRIS Fraud Detection Settings
    |--------------------------------------------------------------------------
    */
    
    'fraud_detection' => [
        'enabled' => env('QRIS_FRAUD_DETECTION_ENABLED', true),
        
        'max_daily_amount' => env('QRIS_MAX_DAILY_AMOUNT', 100000000), // 100 million IDR
        
        'max_transactions_per_hour' => env('QRIS_MAX_TRANSACTIONS_PER_HOUR', 10),
        
        'suspicious_amount_threshold' => env('QRIS_SUSPICIOUS_AMOUNT_THRESHOLD', 10000000), // 10 million IDR
        
        'velocity_check_enabled' => env('QRIS_VELOCITY_CHECK_ENABLED', true),
        
        'geolocation_check_enabled' => env('QRIS_GEOLOCATION_CHECK_ENABLED', false),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | QRIS Logging Settings
    |--------------------------------------------------------------------------
    */
    
    'logging' => [
        'enabled' => env('QRIS_LOGGING_ENABLED', true),
        
        'log_level' => env('QRIS_LOG_LEVEL', 'info'),
        
        'log_channel' => env('QRIS_LOG_CHANNEL', 'qris'),
        
        'log_requests' => env('QRIS_LOG_REQUESTS', true),
        
        'log_responses' => env('QRIS_LOG_RESPONSES', true),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | QRIS Environment Settings
    |--------------------------------------------------------------------------
    */
    
    'environment' => env('QRIS_ENVIRONMENT', 'sandbox'), // sandbox or production
    
    'sandbox_mode' => env('QRIS_SANDBOX_MODE', true),
    
    /*
    |--------------------------------------------------------------------------
    | QRIS API Endpoints
    |--------------------------------------------------------------------------
    */
    
    'api_endpoints' => [
        'sandbox' => [
            'base_url' => 'https://api.sandbox.midtrans.com',
            'snap_url' => 'https://app.sandbox.midtrans.com/snap/snap.js',
        ],
        'production' => [
            'base_url' => 'https://api.midtrans.com',
            'snap_url' => 'https://app.midtrans.com/snap/snap.js',
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | QRIS Retry Settings
    |--------------------------------------------------------------------------
    */
    
    'retry' => [
        'max_attempts' => env('QRIS_MAX_RETRY_ATTEMPTS', 3),
        
        'delay_seconds' => env('QRIS_RETRY_DELAY_SECONDS', 2),
        
        'exponential_backoff' => env('QRIS_EXPONENTIAL_BACKOFF', true),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | QRIS Cache Settings
    |--------------------------------------------------------------------------
    */
    
    'cache' => [
        'enabled' => env('QRIS_CACHE_ENABLED', true),
        
        'ttl' => env('QRIS_CACHE_TTL', 300), // 5 minutes
        
        'prefix' => env('QRIS_CACHE_PREFIX', 'qris_'),
    ],
];
