<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DiggieService;
use App\Models\Product;
use App\Models\PriceList;
use Illuminate\Support\Str;

class DebugDiggieSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diggie:debug-sync {--limit=3}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug DigiFlazz sync process step by step';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Debugging DigiFlazz Sync Process...');
        
        try {
            $diggieService = new DiggieService();
            $products = $diggieService->getProducts();
            
            $this->info('📦 Found ' . count($products) . ' products from DigiFlazz');
            
            // Filter active products
            $activeProducts = array_filter($products, function($product) {
                return is_array($product) && 
                       ($product['seller_product_status'] ?? false) === true && 
                       ($product['buyer_product_status'] ?? false) === true;
            });
            
            $this->info('✅ Active products: ' . count($activeProducts));
            
            $limit = (int) $this->option('limit');
            $testProducts = array_slice($activeProducts, 0, $limit);
            
            $this->info("🧪 Testing with {$limit} products...");
            
            $profitPercentage = config('diggie.default_profit_percentage', 15);
            
            foreach ($testProducts as $index => $productData) {
                $this->info("\n--- Processing Product " . ($index + 1) . " ---");
                $this->line('Name: ' . $productData['product_name']);
                $this->line('Brand: ' . $productData['brand']);
                $this->line('Category: ' . $productData['category']);
                $this->line('SKU: ' . $productData['buyer_sku_code']);
                $this->line('Price: Rp ' . number_format($productData['price']));
                
                try {
                    // Step 1: Create category
                    $categoryName = $productData['category'];
                    $brandName = $productData['brand'];
                    $fullCategoryName = $categoryName . ' - ' . $brandName;
                    $categorySlug = Str::slug($fullCategoryName);
                    
                    $category = \App\Models\Category::firstOrCreate(
                        ['slug' => $categorySlug],
                        [
                            'name' => $fullCategoryName,
                            'slug' => $categorySlug,
                            'description' => 'Auto-generated category for ' . $fullCategoryName,
                            'image_url' => '/images/products/default.jpg',
                            'is_active' => true,
                        ]
                    );
                    
                    $this->line('✅ Category: ' . $category->name . ' (ID: ' . $category->id . ')');
                    
                    // Step 2: Create product (brand level)
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
                            'instructions' => 'Masukkan data yang diperlukan. Proses otomatis dalam 1-3 menit.',
                            'form_fields' => [
                                ['name' => 'user_id', 'label' => 'User ID', 'type' => 'text', 'required' => true],
                            ],
                            'image' => '/images/products/default.jpg',
                        ]
                    );
                    
                    $this->line('✅ Product: ' . $product->name . ' (ID: ' . $product->id . ')');
                    
                    // Step 3: Create price list
                    $basePrice = $productData['price'];
                    $sellingPrice = $basePrice + ($basePrice * $profitPercentage / 100);
                    
                    $priceList = PriceList::updateOrCreate(
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
                    
                    $this->line('✅ Price List: ' . $priceList->name . ' (ID: ' . $priceList->id . ')');
                    $this->line('💰 Base Price: Rp ' . number_format($basePrice));
                    $this->line('💰 Selling Price: Rp ' . number_format($sellingPrice));
                    
                } catch (\Exception $e) {
                    $this->error('❌ Error: ' . $e->getMessage());
                }
            }
            
            $this->newLine();
            $this->info('📊 Final Database Status:');
            $this->line('Categories: ' . \App\Models\Category::count());
            $this->line('Products: ' . \App\Models\Product::count());
            $this->line('Price Lists: ' . \App\Models\PriceList::count());
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Debug failed: ' . $e->getMessage());
            return 1;
        }
    }
}
