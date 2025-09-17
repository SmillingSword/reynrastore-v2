<?php

return [
    'api_url' => env('DIGGIE_API_URL', 'https://api.digiflazz.com/v1'),
    'username' => env('DIGGIE_USERNAME'),
    'api_key' => env('DIGGIE_API_KEY'),
    'dev_key' => env('DIGGIE_DEV_KEY'),
    'production' => env('DIGGIE_PRODUCTION', false),
    'timeout' => env('DIGGIE_TIMEOUT', 30),
    'default_profit_percentage' => env('DIGGIE_DEFAULT_PROFIT', 15),
];
