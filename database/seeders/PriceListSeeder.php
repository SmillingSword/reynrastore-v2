<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\PriceList;

class PriceListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari produk manual untuk menambahkan price list
        $manualProduct = Product::where('type', 'manual')->first();
        
        if ($manualProduct) {
            // Buat beberapa price list untuk produk manual
            $priceLists = [
                [
                    'product_id' => $manualProduct->id,
                    'name' => 'Mobile Legends Diamond 50',
                    'description' => 'Top up 50 Diamond Mobile Legends',
                    'type' => 'manual',
                    'base_price' => 8000,
                    'selling_price' => 9500,
                    'profit_percentage' => 18.75,
                    'seller_status' => true,
                    'buyer_status' => true,
                    'unlimited_stock' => false,
                    'stock' => 100,
                    'is_active' => true,
                ],
                [
                    'product_id' => $manualProduct->id,
                    'name' => 'Mobile Legends Diamond 100',
                    'description' => 'Top up 100 Diamond Mobile Legends',
                    'type' => 'manual',
                    'base_price' => 15000,
                    'selling_price' => 17000,
                    'profit_percentage' => 13.33,
                    'seller_status' => true,
                    'buyer_status' => true,
                    'unlimited_stock' => false,
                    'stock' => 150,
                    'is_active' => true,
                ],
                [
                    'product_id' => $manualProduct->id,
                    'name' => 'Mobile Legends Diamond 200',
                    'description' => 'Top up 200 Diamond Mobile Legends',
                    'type' => 'manual',
                    'base_price' => 28000,
                    'selling_price' => 32000,
                    'profit_percentage' => 14.29,
                    'seller_status' => true,
                    'buyer_status' => true,
                    'unlimited_stock' => false,
                    'stock' => 80,
                    'is_active' => true,
                ],
                [
                    'product_id' => $manualProduct->id,
                    'name' => 'Mobile Legends Diamond 500',
                    'description' => 'Top up 500 Diamond Mobile Legends',
                    'type' => 'manual',
                    'base_price' => 65000,
                    'selling_price' => 75000,
                    'profit_percentage' => 15.38,
                    'seller_status' => true,
                    'buyer_status' => true,
                    'unlimited_stock' => false,
                    'stock' => 50,
                    'is_active' => true,
                ],
                [
                    'product_id' => $manualProduct->id,
                    'name' => 'Mobile Legends Diamond 1000',
                    'description' => 'Top up 1000 Diamond Mobile Legends',
                    'type' => 'manual',
                    'base_price' => 125000,
                    'selling_price' => 145000,
                    'profit_percentage' => 16.00,
                    'seller_status' => true,
                    'buyer_status' => true,
                    'unlimited_stock' => false,
                    'stock' => 30,
                    'is_active' => true,
                ],
            ];

            foreach ($priceLists as $priceListData) {
                PriceList::create($priceListData);
            }

            $this->command->info('Sample price lists created successfully!');
        } else {
            $this->command->warn('No manual product found to create price lists.');
        }
    }
}
