<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DiggieService
{
    private $client;
    private $apiUrl;
    private $apiKey;
    private $username;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => config('diggie.timeout', 30),
            'verify' => true, // Production ready
        ]);
        
        $this->apiUrl = config('diggie.api_url');
        $this->username = config('diggie.username');
        
        // Use production or development key based on config
        $this->apiKey = config('diggie.production') 
            ? config('diggie.api_key') 
            : config('diggie.dev_key');
    }

    /**
     * Get product list from DigiFlazz API
     */
    public function getProducts()
    {
        try {
            $cmd = 'prepaid';
            // Signature formula berdasarkan dokumentasi DigiFlazz untuk price list: md5(username + apiKey + "pricelist")
            $sign = md5($this->username . $this->apiKey . 'pricelist');
            
            $requestData = [
                'cmd' => $cmd,
                'username' => $this->username,
                'sign' => $sign,
            ];
            
            Log::info('DigiFlazz API Request (Price List)', [
                'url' => $this->apiUrl . '/price-list',
                'data' => $requestData,
                'username' => $this->username,
                'api_key_preview' => substr($this->apiKey, 0, 8) . '...',
                'signature_formula' => 'md5(username + apiKey + "pricelist")',
                'signature' => $sign
            ]);
            
            // Use JSON format sesuai dokumentasi DigiFlazz untuk price-list endpoint
            $response = $this->client->post($this->apiUrl . '/price-list', [
                'json' => $requestData,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            Log::info('DigiFlazz API Response (Price List)', [
                'response_full' => $data,
                'response_preview' => [
                    'rc' => $data['data']['rc'] ?? 'N/A',
                    'message' => $data['data']['message'] ?? 'N/A',
                    'data_count' => isset($data['data']['prepaid']) ? count($data['data']['prepaid']) : 0,
                    'has_data_key' => isset($data['data']),
                    'data_keys' => isset($data['data']) ? array_keys($data['data']) : []
                ]
            ]);
            
            // Check if response is successful
            if (isset($data['data']) && is_array($data['data'])) {
                // Check for response code if it exists, but still return data if it doesn't
                if (isset($data['data']['rc']) && $data['data']['rc'] !== '00') {
                     throw new \Exception('DigiFlazz API Error: ' . ($data['data']['message'] ?? 'Invalid response code'));
                }
                return $data['data']['prepaid'] ?? $data['data']; // Return data directly if prepaid key is missing
            }
            
            // Check if data is directly in root level
            if (isset($data['prepaid']) && is_array($data['prepaid'])) {
                return $data['prepaid'];
            }
            
            throw new \Exception('DigiFlazz API Error: No products available or invalid response structure');
        } catch (RequestException $e) {
            $responseBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response body';
            Log::error('DigiFlazz API Error (getProducts)', [
                'error' => $e->getMessage(),
                'response' => $responseBody,
                'status_code' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : 'N/A'
            ]);
            throw $e;
        }
    }

    /**
     * Get product price from Diggie API
     */
    public function getProductPrice($productCode)
    {
        try {
            $response = $this->client->post($this->apiUrl . '/api/price', [
                'form_params' => [
                    'key' => $this->apiKey,
                    'sign' => $this->generateSignature('price'),
                    'service' => $productCode,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if ($data['result'] === true) {
                return $data['data'];
            }
            
            throw new \Exception('Failed to fetch price: ' . $data['message']);
        } catch (RequestException $e) {
            Log::error('Diggie API Error (getProductPrice): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process order through Diggie API
     */
    public function processOrder(OrderItem $orderItem)
    {
        try {
            $product = $orderItem->product;
            $order = $orderItem->order;
            
            // Prepare order data
            $orderData = [
                'key' => $this->apiKey,
                'sign' => $this->generateSignature('order'),
                'service' => $product->diggie_product_id,
                'data' => $this->formatOrderData($orderItem),
                'reff_id' => $order->order_number . '-' . $orderItem->id,
            ];

            $response = $this->client->post($this->apiUrl . '/api/order', [
                'form_params' => $orderData
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if ($data['result'] === true) {
                // Update order item with Diggie transaction ID
                $orderItem->update([
                    'diggie_transaction_id' => $data['data']['id'],
                    'status' => 'processing',
                    'processed_at' => now(),
                ]);
                
                Log::info('Diggie order processed successfully', [
                    'order_item_id' => $orderItem->id,
                    'diggie_id' => $data['data']['id']
                ]);
                
                return $data['data'];
            }
            
            throw new \Exception('Failed to process order: ' . $data['message']);
        } catch (RequestException $e) {
            Log::error('Diggie API Error (processOrder): ' . $e->getMessage());
            
            // Update order item status to failed
            $orderItem->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Check order status from Diggie API
     */
    public function checkOrderStatus($diggieTransactionId)
    {
        try {
            $response = $this->client->post($this->apiUrl . '/api/status', [
                'form_params' => [
                    'key' => $this->apiKey,
                    'sign' => $this->generateSignature('status'),
                    'id' => $diggieTransactionId,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if ($data['result'] === true) {
                return $data['data'];
            }
            
            throw new \Exception('Failed to check status: ' . $data['message']);
        } catch (RequestException $e) {
            Log::error('Diggie API Error (checkOrderStatus): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update product prices from Diggie API
     */
    public function updateProductPrices()
    {
        try {
            $products = Product::where('type', 'diggie')
                ->whereNotNull('diggie_product_id')
                ->get();

            $updatedCount = 0;
            $profitPercentage = Setting::getValue('diggie_profit_percentage', 15);

            foreach ($products as $product) {
                try {
                    $priceData = $this->getProductPrice($product->diggie_product_id);
                    
                    $basePrice = $priceData['price'];
                    $sellingPrice = $basePrice + ($basePrice * $profitPercentage / 100);
                    
                    $product->update([
                        'base_price' => $basePrice,
                        'selling_price' => $sellingPrice,
                        'profit_percentage' => $profitPercentage,
                        'updated_at' => now(),
                    ]);
                    
                    $updatedCount++;
                    
                    // Add small delay to avoid rate limiting
                    usleep(100000); // 0.1 second
                } catch (\Exception $e) {
                    Log::warning('Failed to update price for product: ' . $product->name, [
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            Log::info('Product prices updated', ['count' => $updatedCount]);
            return $updatedCount;
        } catch (\Exception $e) {
            Log::error('Diggie price update error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sync all products from DigiFlazz with new structure
     */
    public function syncAllProducts()
    {
        try {
            $products = $this->getProducts();
            $syncedProductsCount = 0;
            $syncedPriceListsCount = 0;
            $profitPercentage = config('diggie.default_profit_percentage', 4);

            foreach ($products as $productData) {
                try {
                    // Ensure productData is array
                    if (!is_array($productData)) {
                        continue;
                    }
                    
                    // Skip if product is not active (both buyer and seller must be active)
                    $sellerStatus = $productData['seller_product_status'] ?? false;
                    $buyerStatus = $productData['buyer_product_status'] ?? false;
                    
                    if ($sellerStatus !== true || $buyerStatus !== true) {
                        continue;
                    }

                    // Step 1: Create or find Product (Brand level)
                    $category = $this->determineCategory($productData);
                    $brandName = $productData['brand'];
                    
                    $product = Product::firstOrCreate(
                        [
                            'name' => $brandName,
                            'category_id' => $category->id,
                            'type' => 'diggie'
                        ],
                        [
                            'slug' => Str::slug($brandName),
                            'brand' => $brandName,
                            'description' => 'Produk ' . $brandName . ' dari kategori ' . $productData['category'],
                            'is_active' => true,
                            'has_price_lists' => true,
                            'instructions' => $this->generateInstructions($category->slug),
                            'form_fields' => $this->generateFormFields($category->slug),
                            'image' => $this->getProductImage($category->slug),
                        ]
                    );

                    if ($product->wasRecentlyCreated) {
                        $syncedProductsCount++;
                        Log::info('New product created', [
                            'product_name' => $brandName,
                            'category' => $category->name
                        ]);
                    }

                    // Step 2: Create or update PriceList (Individual items)
                    $basePrice = $productData['price'];
                    $sellingPrice = $basePrice + ($basePrice * $profitPercentage / 100);

                    $priceList = \App\Models\PriceList::updateOrCreate(
                        ['diggie_sku_code' => $productData['buyer_sku_code']],
                        [
                            'product_id' => $product->id,
                            'name' => $productData['product_name'],
                            'type' => $productData['type'] ?? 'Umum',
                            'description' => $productData['desc'] ?? $productData['product_name'],
                            'base_price' => $basePrice,
                            'selling_price' => $sellingPrice,
                            'profit_percentage' => $profitPercentage,
                            'seller_status' => $productData['seller_product_status'],
                            'buyer_status' => $productData['buyer_product_status'],
                            'unlimited_stock' => $productData['unlimited_stock'] ?? true,
                            'stock' => $productData['stock'] ?? 0,
                            'multi' => $productData['multi'] ?? true,
                            'start_cut_off' => $productData['start_cut_off'] ?? null,
                            'end_cut_off' => $productData['end_cut_off'] ?? null,
                            'is_active' => true,
                        ]
                    );

                    if ($priceList->wasRecentlyCreated) {
                        $syncedPriceListsCount++;
                    }
                    
                    Log::info('Price list synced', [
                        'product_name' => $productData['product_name'],
                        'brand' => $brandName,
                        'price' => $basePrice,
                        'selling_price' => $sellingPrice
                    ]);
                    
                    // Add small delay to avoid rate limiting
                    usleep(50000); // 0.05 second
                } catch (\Exception $e) {
                    Log::warning('Failed to sync product: ' . ($productData['product_name'] ?? 'Unknown'), [
                        'error' => $e->getMessage(),
                        'product_data' => $productData
                    ]);
                }
            }
            
            Log::info('DigiFlazz sync completed', [
                'products_synced' => $syncedProductsCount,
                'price_lists_synced' => $syncedPriceListsCount,
                'total_processed' => count($products)
            ]);
            
            return [
                'products' => $syncedProductsCount,
                'price_lists' => $syncedPriceListsCount,
                'total' => count($products)
            ];
        } catch (\Exception $e) {
            Log::error('DigiFlazz sync error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate signature for Diggie API
     */
    private function generateSignature($endpoint)
    {
        return md5($this->username . $this->apiKey . $endpoint);
    }

    /**
     * Determine category based on product data - Auto create from DigiFlazz
     */
    private function determineCategory($productData)
    {
        $categoryName = $productData['category'];
        $brandName = $productData['brand'];
        
        // Create category name: "Category - Brand" (e.g., "Games - Mobile Legends")
        $fullCategoryName = $categoryName . ' - ' . $brandName;
        $categorySlug = Str::slug($fullCategoryName);
        
        // Find or create category
        $category = \App\Models\Category::firstOrCreate(
            ['slug' => $categorySlug],
            [
                'name' => $fullCategoryName,
                'slug' => $categorySlug,
                'description' => 'Auto-generated category for ' . $fullCategoryName,
                'image_url' => $this->getProductImage($categorySlug),
                'is_active' => true,
            ]
        );
        
        return $category;
    }

    /**
     * Generate form fields based on category
     */
    private function generateFormFields($categorySlug)
    {
        $formFields = [
            'mobile-legends' => [
                ['name' => 'user_id', 'label' => 'User ID', 'type' => 'text', 'required' => true],
                ['name' => 'zone_id', 'label' => 'Zone ID', 'type' => 'text', 'required' => true],
            ],
            'free-fire' => [
                ['name' => 'user_id', 'label' => 'User ID', 'type' => 'text', 'required' => true],
            ],
            'pubg-mobile' => [
                ['name' => 'user_id', 'label' => 'User ID', 'type' => 'text', 'required' => true],
            ],
            'genshin-impact' => [
                ['name' => 'uid', 'label' => 'UID', 'type' => 'text', 'required' => true],
                ['name' => 'server', 'label' => 'Server', 'type' => 'select', 'required' => true, 'options' => [
                    'os_asia' => 'Asia',
                    'os_usa' => 'America',
                    'os_euro' => 'Europe',
                    'os_cht' => 'TW/HK/MO'
                ]],
            ],
            'valorant' => [
                ['name' => 'riot_id', 'label' => 'Riot ID', 'type' => 'text', 'required' => true],
            ],
            'steam-wallet' => [
                ['name' => 'steam_id', 'label' => 'Steam ID', 'type' => 'text', 'required' => true],
            ],
        ];

        return $formFields[$categorySlug] ?? [
            ['name' => 'user_id', 'label' => 'User ID', 'type' => 'text', 'required' => true],
        ];
    }

    /**
     * Generate instructions based on category
     */
    private function generateInstructions($categorySlug)
    {
        $instructions = [
            'mobile-legends' => 'Masukkan User ID dan Zone ID Mobile Legends Anda. Proses otomatis dalam 1-3 menit.',
            'free-fire' => 'Masukkan User ID Free Fire Anda. Proses otomatis dalam 1-3 menit.',
            'pubg-mobile' => 'Masukkan User ID PUBG Mobile Anda. Proses otomatis dalam 1-3 menit.',
            'genshin-impact' => 'Masukkan UID dan pilih Server Genshin Impact Anda. Proses otomatis dalam 1-3 menit.',
            'valorant' => 'Masukkan Riot ID Valorant Anda. Proses otomatis dalam 1-3 menit.',
            'steam-wallet' => 'Masukkan Steam ID Anda. Proses otomatis dalam 1-3 menit.',
        ];

        return $instructions[$categorySlug] ?? 'Masukkan data yang diperlukan. Proses otomatis dalam 1-3 menit.';
    }

    /**
     * Get product image based on category
     */
    private function getProductImage($categorySlug)
    {
        $images = [
            'mobile-legends' => '/images/products/ml-diamond.jpg',
            'free-fire' => '/images/products/ff-diamond.jpg',
            'pubg-mobile' => '/images/products/pubg-uc.jpg',
            'genshin-impact' => '/images/products/genshin-crystal.jpg',
            'valorant' => '/images/products/valorant-vp.jpg',
            'steam-wallet' => '/images/products/steam-wallet.jpg',
        ];

        return $images[$categorySlug] ?? '/images/products/default.jpg';
    }

    /**
     * Format order data for Diggie API
     */
    private function formatOrderData(OrderItem $orderItem)
    {
        $formData = $orderItem->form_data;
        $product = $orderItem->product;
        
        // Format based on product type
        switch ($product->category->slug) {
            case 'mobile-legends':
                return $formData['user_id'] . '|' . $formData['zone_id'];
                
            case 'free-fire':
                return $formData['user_id'];
                
            case 'pubg-mobile':
                return $formData['user_id'];
                
            case 'genshin-impact':
                return $formData['uid'] . '|' . $formData['server'];
                
            default:
                // For other games, join all form data with |
                return implode('|', array_values($formData));
        }
    }

    /**
     * Get balance from DigiFlazz API
     */
    public function getBalance()
    {
        try {
            // Sesuai dokumentasi DigiFlazz
            $cmd = 'deposit';
            $sign = md5($this->username . $this->apiKey . 'depo');
            
            $requestData = [
                'cmd' => $cmd,
                'username' => $this->username,
                'sign' => $sign,
            ];
            
            Log::info('DigiFlazz API Request (Balance)', [
                'url' => $this->apiUrl . '/cek-saldo',
                'data' => $requestData,
                'username' => $this->username,
                'signature_formula' => 'md5(username + apiKey + "depo")',
                'signature' => $sign
            ]);
            
            // Use JSON format sesuai dokumentasi DigiFlazz
            $response = $this->client->post($this->apiUrl . '/cek-saldo', [
                'json' => $requestData,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            Log::info('DigiFlazz API Response (Balance)', [
                'response' => $data
            ]);
            
            // Check response structure - DigiFlazz mengembalikan data langsung
            if (isset($data['data']['deposit'])) {
                return [
                    'balance' => $data['data']['deposit'],
                    'message' => 'Success'
                ];
            }
            
            // Check for error response
            if (isset($data['data']['rc']) && $data['data']['rc'] !== '00') {
                throw new \Exception('DigiFlazz Balance API Error: ' . ($data['data']['message'] ?? 'API Error'));
            }
            
            throw new \Exception('DigiFlazz Balance API Error: Unexpected response structure');
        } catch (RequestException $e) {
            $responseBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response body';
            Log::error('DigiFlazz API Error (getBalance)', [
                'error' => $e->getMessage(),
                'response' => $responseBody,
                'status_code' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : 'N/A'
            ]);
            throw $e;
        }
    }
}
