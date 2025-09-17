<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed application data
        $this->call([
            SettingSeeder::class,
            // CategorySeeder::class,
            // ProductSeeder::class,
            AdminUserSeeder::class,
            // OrderSeeder::class,
            // PriceListSeeder::class,
        ]);
    }
}
