<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $mlCategory = Category::where('slug', 'mobile-legends')->first();
        $ffCategory = Category::where('slug', 'free-fire')->first();
        $pubgCategory = Category::where('slug', 'pubg-mobile')->first();
        $genshinCategory = Category::where('slug', 'genshin-impact')->first();
        $valorantCategory = Category::where('slug', 'valorant')->first();

        $products = [
            // Mobile Legends Products
            [
                'category_id' => $mlCategory->id,
                'name' => '86 Diamond Mobile Legends',
                'slug' => '86-diamond-mobile-legends',
                'description' => 'Top up 86 Diamond Mobile Legends dengan proses cepat dan aman',
                'image' => '/images/products/ml-86-diamond.jpg',
                'base_price' => 20000,
                'profit_percentage' => 15,
                'selling_price' => 23000,
                'sku' => 'ML-86-DIAMOND',
                'type' => 'diggie',
                'diggie_product_id' => 'ml_86_diamond',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
                'form_fields' => [
                    [
                        'name' => 'user_id',
                        'label' => 'User ID',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Masukkan User ID'
                    ],
                    [
                        'name' => 'zone_id',
                        'label' => 'Zone ID',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Masukkan Zone ID'
                    ]
                ],
                'instructions' => 'Masukkan User ID dan Zone ID yang benar. Contoh: User ID: 123456789, Zone ID: 1234'
            ],
            [
                'category_id' => $mlCategory->id,
                'name' => '172 Diamond Mobile Legends',
                'slug' => '172-diamond-mobile-legends',
                'description' => 'Top up 172 Diamond Mobile Legends dengan proses cepat dan aman',
                'image' => '/images/products/ml-172-diamond.jpg',
                'base_price' => 40000,
                'profit_percentage' => 15,
                'selling_price' => 46000,
                'sku' => 'ML-172-DIAMOND',
                'type' => 'diggie',
                'diggie_product_id' => 'ml_172_diamond',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
                'form_fields' => [
                    [
                        'name' => 'user_id',
                        'label' => 'User ID',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Masukkan User ID'
                    ],
                    [
                        'name' => 'zone_id',
                        'label' => 'Zone ID',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Masukkan Zone ID'
                    ]
                ],
                'instructions' => 'Masukkan User ID dan Zone ID yang benar. Contoh: User ID: 123456789, Zone ID: 1234'
            ],

            // Free Fire Products
            [
                'category_id' => $ffCategory->id,
                'name' => '70 Diamond Free Fire',
                'slug' => '70-diamond-free-fire',
                'description' => 'Top up 70 Diamond Free Fire dengan proses cepat dan aman',
                'image' => '/images/products/ff-70-diamond.jpg',
                'base_price' => 10000,
                'profit_percentage' => 15,
                'selling_price' => 11500,
                'sku' => 'FF-70-DIAMOND',
                'type' => 'diggie',
                'diggie_product_id' => 'ff_70_diamond',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
                'form_fields' => [
                    [
                        'name' => 'user_id',
                        'label' => 'User ID',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Masukkan User ID Free Fire'
                    ]
                ],
                'instructions' => 'Masukkan User ID Free Fire yang benar. User ID dapat dilihat di profil game.'
            ],
            [
                'category_id' => $ffCategory->id,
                'name' => '140 Diamond Free Fire',
                'slug' => '140-diamond-free-fire',
                'description' => 'Top up 140 Diamond Free Fire dengan proses cepat dan aman',
                'image' => '/images/products/ff-140-diamond.jpg',
                'base_price' => 20000,
                'profit_percentage' => 15,
                'selling_price' => 23000,
                'sku' => 'FF-140-DIAMOND',
                'type' => 'diggie',
                'diggie_product_id' => 'ff_140_diamond',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
                'form_fields' => [
                    [
                        'name' => 'user_id',
                        'label' => 'User ID',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Masukkan User ID Free Fire'
                    ]
                ],
                'instructions' => 'Masukkan User ID Free Fire yang benar. User ID dapat dilihat di profil game.'
            ],

            // PUBG Mobile Products
            [
                'category_id' => $pubgCategory->id,
                'name' => '60 UC PUBG Mobile',
                'slug' => '60-uc-pubg-mobile',
                'description' => 'Top up 60 UC PUBG Mobile dengan proses cepat dan aman',
                'image' => '/images/products/pubg-60-uc.jpg',
                'base_price' => 15000,
                'profit_percentage' => 15,
                'selling_price' => 17250,
                'sku' => 'PUBG-60-UC',
                'type' => 'diggie',
                'diggie_product_id' => 'pubg_60_uc',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
                'form_fields' => [
                    [
                        'name' => 'user_id',
                        'label' => 'User ID',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Masukkan User ID PUBG Mobile'
                    ]
                ],
                'instructions' => 'Masukkan User ID PUBG Mobile yang benar. User ID dapat dilihat di profil game.'
            ],

            // Genshin Impact Products
            [
                'category_id' => $genshinCategory->id,
                'name' => '60 Genesis Crystal',
                'slug' => '60-genesis-crystal',
                'description' => 'Top up 60 Genesis Crystal Genshin Impact',
                'image' => '/images/products/genshin-60-crystal.jpg',
                'base_price' => 16000,
                'profit_percentage' => 15,
                'selling_price' => 18400,
                'sku' => 'GI-60-CRYSTAL',
                'type' => 'diggie',
                'diggie_product_id' => 'gi_60_crystal',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
                'form_fields' => [
                    [
                        'name' => 'uid',
                        'label' => 'UID',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Masukkan UID Genshin Impact'
                    ],
                    [
                        'name' => 'server',
                        'label' => 'Server',
                        'type' => 'select',
                        'required' => true,
                        'options' => [
                            ['value' => 'asia', 'label' => 'Asia'],
                            ['value' => 'america', 'label' => 'America'],
                            ['value' => 'europe', 'label' => 'Europe'],
                            ['value' => 'cht', 'label' => 'TW/HK/MO']
                        ]
                    ]
                ],
                'instructions' => 'Masukkan UID dan pilih server yang benar. UID dapat dilihat di menu Paimon.'
            ],

            // Valorant Products
            [
                'category_id' => $valorantCategory->id,
                'name' => '420 VP Valorant',
                'slug' => '420-vp-valorant',
                'description' => 'Top up 420 VP Valorant untuk skin dan battle pass',
                'image' => '/images/products/valorant-420-vp.jpg',
                'base_price' => 35000,
                'profit_percentage' => 15,
                'selling_price' => 40250,
                'sku' => 'VAL-420-VP',
                'type' => 'manual',
                'is_active' => true,
                'is_featured' => false,
                'stock' => 100,
                'sort_order' => 1,
                'form_fields' => [
                    [
                        'name' => 'riot_id',
                        'label' => 'Riot ID',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Masukkan Riot ID (contoh: Username#TAG)'
                    ]
                ],
                'instructions' => 'Masukkan Riot ID lengkap dengan tag. Contoh: Username#1234. Proses manual 1-24 jam.'
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['slug' => $product['slug']],
                $product
            );
        }
    }
}
