<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Mobile Legends',
                'slug' => 'mobile-legends',
                'description' => 'Top up Diamond Mobile Legends dengan harga terbaik dan proses cepat',
                'image' => '/images/categories/mobile-legends.jpg',
                'icon' => 'gamepad-2',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Free Fire',
                'slug' => 'free-fire',
                'description' => 'Top up Diamond Free Fire murah dan aman',
                'image' => '/images/categories/free-fire.jpg',
                'icon' => 'zap',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'PUBG Mobile',
                'slug' => 'pubg-mobile',
                'description' => 'Top up UC PUBG Mobile dengan berbagai nominal',
                'image' => '/images/categories/pubg-mobile.jpg',
                'icon' => 'target',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Genshin Impact',
                'slug' => 'genshin-impact',
                'description' => 'Top up Genesis Crystal Genshin Impact',
                'image' => '/images/categories/genshin-impact.jpg',
                'icon' => 'sparkles',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Valorant',
                'slug' => 'valorant',
                'description' => 'Top up VP Valorant untuk skin dan battle pass',
                'image' => '/images/categories/valorant.jpg',
                'icon' => 'crosshair',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Steam Wallet',
                'slug' => 'steam-wallet',
                'description' => 'Steam Wallet Code untuk membeli game di Steam',
                'image' => '/images/categories/steam.jpg',
                'icon' => 'steam',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Google Play',
                'slug' => 'google-play',
                'description' => 'Google Play Gift Card untuk berbagai kebutuhan',
                'image' => '/images/categories/google-play.jpg',
                'icon' => 'smartphone',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'App Store',
                'slug' => 'app-store',
                'description' => 'iTunes Gift Card untuk pengguna iOS',
                'image' => '/images/categories/app-store.jpg',
                'icon' => 'apple',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Roblox',
                'slug' => 'roblox',
                'description' => 'Top up Robux untuk berbagai item di Roblox',
                'image' => '/images/categories/roblox.jpg',
                'icon' => 'box',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Minecraft',
                'slug' => 'minecraft',
                'description' => 'Minecoins untuk Minecraft Bedrock Edition',
                'image' => '/images/categories/minecraft.jpg',
                'icon' => 'cube',
                'is_active' => true,
                'sort_order' => 10,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
