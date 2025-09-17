<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada kategori dan produk
        $category = Category::firstOrCreate([
            'name' => 'Mobile Legends',
            'slug' => 'mobile-legends',
            'is_active' => true
        ]);

        // Buat produk manual untuk testing
        $manualProduct = Product::firstOrCreate([
            'name' => 'Mobile Legends Diamond 100',
            'slug' => 'ml-diamond-100'
        ], [
            'category_id' => $category->id,
            'description' => 'Top up 100 Diamond Mobile Legends',
            'base_price' => 15000,
            'selling_price' => 17000,
            'profit_percentage' => 13.33,
            'type' => 'manual',
            'is_active' => true,
            'stock' => 100,
            'sort_order' => 1
        ]);

        // Buat produk digiflazz untuk testing
        $digiflazzProduct = Product::firstOrCreate([
            'name' => 'Free Fire Diamond 70',
            'slug' => 'ff-diamond-70'
        ], [
            'category_id' => $category->id,
            'description' => 'Top up 70 Diamond Free Fire',
            'base_price' => 10000,
            'selling_price' => 12000,
            'profit_percentage' => 20,
            'type' => 'diggie',
            'diggie_product_id' => 'FF70',
            'is_active' => true,
            'stock' => 999,
            'sort_order' => 2
        ]);

        // Data contoh orders dengan berbagai status
        $orders = [
            // Order pending manual
            [
                'order_number' => 'ORD-' . date('Ymd') . '-001',
                'customer_name' => 'Ahmad Rizki',
                'customer_email' => 'ahmad.rizki@email.com',
                'customer_phone' => '081234567890',
                'subtotal' => 17000,
                'total_amount' => 17000,
                'status' => 'pending',
                'payment_status' => 'paid',
                'payment_method' => 'bank_transfer',
                'customer_data' => json_encode([
                    'user_id' => '123456789', 
                    'zone_id' => '1234',
                    'type' => 'manual'
                ]),
                'created_at' => Carbon::now()->subHours(2),
                'product' => $manualProduct
            ],
            // Order processing manual
            [
                'order_number' => 'ORD-' . date('Ymd') . '-002',
                'customer_name' => 'Siti Nurhaliza',
                'customer_email' => 'siti.nur@email.com',
                'customer_phone' => '081234567891',
                'subtotal' => 17000,
                'total_amount' => 17000,
                'status' => 'processing',
                'payment_status' => 'paid',
                'payment_method' => 'e_wallet',
                'customer_data' => json_encode([
                    'user_id' => '987654321', 
                    'zone_id' => '5678',
                    'type' => 'manual'
                ]),
                'created_at' => Carbon::now()->subHours(4),
                'product' => $manualProduct
            ],
            // Order completed manual
            [
                'order_number' => 'ORD-' . date('Ymd') . '-003',
                'customer_name' => 'Budi Santoso',
                'customer_email' => 'budi.santoso@email.com',
                'customer_phone' => '081234567892',
                'subtotal' => 17000,
                'total_amount' => 17000,
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => 'bank_transfer',
                'customer_data' => json_encode([
                    'user_id' => '555666777', 
                    'zone_id' => '9999',
                    'type' => 'manual'
                ]),
                'created_at' => Carbon::now()->subDay(),
                'product' => $manualProduct
            ],
            // Order processing digiflazz (otomatis)
            [
                'order_number' => 'ORD-' . date('Ymd') . '-004',
                'customer_name' => 'Maya Sari',
                'customer_email' => 'maya.sari@email.com',
                'customer_phone' => '081234567893',
                'subtotal' => 12000,
                'total_amount' => 12000,
                'status' => 'processing',
                'payment_status' => 'paid',
                'payment_method' => 'e_wallet',
                'customer_data' => json_encode([
                    'user_id' => '111222333',
                    'type' => 'auto'
                ]),
                'created_at' => Carbon::now()->subHours(1),
                'product' => $digiflazzProduct
            ],
            // Order completed digiflazz
            [
                'order_number' => 'ORD-' . date('Ymd') . '-005',
                'customer_name' => 'Andi Wijaya',
                'customer_email' => 'andi.wijaya@email.com',
                'customer_phone' => '081234567894',
                'subtotal' => 12000,
                'total_amount' => 12000,
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => 'bank_transfer',
                'customer_data' => json_encode([
                    'user_id' => '444555666',
                    'type' => 'auto'
                ]),
                'created_at' => Carbon::now()->subDays(2),
                'product' => $digiflazzProduct
            ],
            // Order failed
            [
                'order_number' => 'ORD-' . date('Ymd') . '-006',
                'customer_name' => 'Rina Kartika',
                'customer_email' => 'rina.kartika@email.com',
                'customer_phone' => '081234567895',
                'subtotal' => 17000,
                'total_amount' => 17000,
                'status' => 'failed',
                'payment_status' => 'paid',
                'payment_method' => 'e_wallet',
                'customer_data' => json_encode([
                    'user_id' => '777888999', 
                    'zone_id' => '1111',
                    'type' => 'manual'
                ]),
                'created_at' => Carbon::now()->subDays(3),
                'product' => $manualProduct
            ],
            // Order cancelled
            [
                'order_number' => 'ORD-' . date('Ymd') . '-007',
                'customer_name' => 'Dedi Kurniawan',
                'customer_email' => 'dedi.kurniawan@email.com',
                'customer_phone' => '081234567896',
                'subtotal' => 17000,
                'total_amount' => 17000,
                'status' => 'cancelled',
                'payment_status' => 'paid',
                'payment_method' => 'bank_transfer',
                'customer_data' => json_encode([
                    'user_id' => '123789456', 
                    'zone_id' => '2222',
                    'type' => 'manual'
                ]),
                'created_at' => Carbon::now()->subDays(5),
                'product' => $manualProduct
            ],
            // Order pending untuk minggu lalu (untuk testing chart)
            [
                'order_number' => 'ORD-' . date('Ymd', strtotime('-7 days')) . '-001',
                'customer_name' => 'Lisa Permata',
                'customer_email' => 'lisa.permata@email.com',
                'customer_phone' => '081234567897',
                'subtotal' => 12000,
                'total_amount' => 12000,
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => 'e_wallet',
                'customer_data' => json_encode([
                    'user_id' => '999888777',
                    'type' => 'auto'
                ]),
                'created_at' => Carbon::now()->subDays(7),
                'product' => $digiflazzProduct
            ]
        ];

        foreach ($orders as $orderData) {
            $product = $orderData['product'];
            $orderStatus = $orderData['status'];
            unset($orderData['product']);

            // Check if order already exists
            $existingOrder = Order::where('order_number', $orderData['order_number'])->first();
            if ($existingOrder) {
                continue; // Skip if order already exists
            }

            // Buat order
            $order = Order::create($orderData);

            // Buat order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->slug, // Menggunakan slug karena sku mungkin tidak ada
                'unit_price' => $product->selling_price,
                'quantity' => 1,
                'total_price' => $product->selling_price,
                'status' => $orderStatus === 'completed' ? 'completed' : 'pending'
            ]);
        }

        $this->command->info('Sample orders created successfully!');
    }
}
