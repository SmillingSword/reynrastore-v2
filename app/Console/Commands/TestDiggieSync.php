<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DiggieService;

class TestDiggieSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diggie:test-sync {--limit=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test DigiFlazz sync with new structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Testing DigiFlazz Sync with New Structure...');
        
        try {
            $diggieService = new DiggieService();
            $products = $diggieService->getProducts();
            
            $this->info('📦 Found ' . count($products) . ' products from DigiFlazz');
            
            // Show sample product data
            if (count($products) > 0) {
                $sample = $products[0];
                $this->info('📋 Sample Product Data:');
                $this->line('Name: ' . $sample['product_name']);
                $this->line('Brand: ' . $sample['brand']);
                $this->line('Category: ' . $sample['category']);
                $this->line('Price: Rp ' . number_format($sample['price']));
                $this->line('Seller Status: ' . ($sample['seller_product_status'] ? 'Active' : 'Inactive'));
                $this->line('Buyer Status: ' . ($sample['buyer_product_status'] ? 'Active' : 'Inactive'));
                $this->newLine();
            }
            
            // Test sync with limit
            $limit = (int) $this->option('limit');
            $this->info("🔄 Testing sync with limit: {$limit}");
            
            $activeProducts = array_filter($products, function($product) {
                return $product['seller_product_status'] === true && $product['buyer_product_status'] === true;
            });
            
            $this->info('✅ Active products: ' . count($activeProducts));
            
            if (count($activeProducts) > 0) {
                $testProducts = array_slice($activeProducts, 0, $limit);
                $this->info("🧪 Testing with {$limit} active products...");
                
                $result = $diggieService->syncAllProducts();
                
                $this->info('✅ Sync completed!');
                $this->table(
                    ['Type', 'Count'],
                    [
                        ['Products Created', $result['products']],
                        ['Price Lists Created', $result['price_lists']],
                        ['Total Processed', $result['total']],
                    ]
                );
                
                // Show created data
                $this->info('📊 Database Status:');
                $this->line('Categories: ' . \App\Models\Category::count());
                $this->line('Products: ' . \App\Models\Product::count());
                $this->line('Price Lists: ' . \App\Models\PriceList::count());
                
            } else {
                $this->warn('⚠️  No active products found to sync');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
