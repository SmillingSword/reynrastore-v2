<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DiggieService;
use Illuminate\Support\Facades\Log;

class SyncDiggieProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diggie:sync-products {--limit=50 : Limit number of products to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all products from DigiFlazz API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting DigiFlazz product sync with new structure...');
        
        try {
            $diggieService = new DiggieService();
            
            // Use DiggieService to sync all products (it handles everything now)
            $result = $diggieService->syncAllProducts();
            
            $this->newLine();
            $this->info('✅ Sync completed!');
            
            $this->table(
                ['Type', 'Count'],
                [
                    ['Products Created', $result['products']],
                    ['Price Lists Created', $result['price_lists']],
                    ['Total Processed', $result['total']],
                ]
            );
            
            // Show database status
            $this->info('📊 Database Status:');
            $this->line('Categories: ' . \App\Models\Category::count());
            $this->line('Products: ' . \App\Models\Product::count());
            $this->line('Price Lists: ' . \App\Models\PriceList::count());
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to sync products: ' . $e->getMessage());
            Log::error('DigiFlazz sync command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }
    
    /**
     * Determine category based on product data
     */
    private function determineCategory($productData)
    {
        $productName = strtolower($productData['product_name'] ?? '');
        $brand = strtolower($productData['brand'] ?? '');
        
        // Map DigiFlazz products to our categories
        $categoryMappings = [
            'mobile-legends' => ['mobile legends', 'ml', 'moonton'],
            'free-fire' => ['free fire', 'ff', 'garena'],
            'pubg-mobile' => ['pubg', 'pubg mobile', 'tencent'],
            'genshin-impact' => ['genshin', 'genshin impact', 'mihoyo'],
            'valorant' => ['valorant', 'riot'],
            'steam-wallet' => ['steam', 'steam wallet'],
        ];
        
        foreach ($categoryMappings as $categorySlug => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($productName, $keyword) !== false || strpos($brand, $keyword) !== false) {
                    return \App\Models\Category::where('slug', $categorySlug)->first();
                }
            }
        }
        
        return null; // Category not supported
    }
    
    /**
     * Sync individual product
     */
    private function syncProduct($productData, $category)
    {
        try {
            $profitPercentage = config('diggie.default_profit_percentage', 15);
            
            // Calculate selling price with profit
            $basePrice = $productData['price'] ?? 0;
            $sellingPrice = $basePrice + ($basePrice * $profitPercentage / 100);
            
            // Create or update product
            $product = \App\Models\Product::updateOrCreate(
                ['diggie_product_id' => $productData['buyer_sku_code']],
                [
                    'name' => $productData['product_name'],
                    'slug' => \Illuminate\Support\Str::slug($productData['product_name']),
                    'description' => $productData['desc'] ?? $productData['product_name'],
                    'category_id' => $category->id,
                    'base_price' => $basePrice,
                    'selling_price' => $sellingPrice,
                    'profit_percentage' => $profitPercentage,
                    'type' => 'diggie',
                    'status' => 'active',
                    'diggie_product_id' => $productData['buyer_sku_code'],
                    'instructions' => $this->generateInstructions($category->slug),
                    'form_fields' => $this->generateFormFields($category->slug),
                    'image_url' => $this->getProductImage($category->slug),
                ]
            );
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to sync product: ' . $e->getMessage(), [
                'product_data' => $productData
            ]);
            return false;
        }
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
}
