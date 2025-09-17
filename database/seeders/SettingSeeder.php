<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'Reynra Store',
                'type' => 'string',
                'description' => 'Website name',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'site_description',
                'value' => 'Top up game terpercaya dengan harga terbaik dan proses cepat',
                'type' => 'string',
                'description' => 'Website description',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'site_logo',
                'value' => '/images/logo/logo.png',
                'type' => 'string',
                'description' => 'Website logo URL',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'contact_email',
                'value' => 'support@reynrastore.com',
                'type' => 'string',
                'description' => 'Contact email address',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'contact_phone',
                'value' => '+62812-3456-7890',
                'type' => 'string',
                'description' => 'Contact phone number',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'contact_whatsapp',
                'value' => '+6281234567890',
                'type' => 'string',
                'description' => 'WhatsApp contact number',
                'group' => 'general',
                'is_public' => true,
            ],

            // Business Settings
            [
                'key' => 'default_profit_percentage',
                'value' => '15',
                'type' => 'float',
                'description' => 'Default profit percentage for products',
                'group' => 'business',
                'is_public' => false,
            ],
            [
                'key' => 'tax_percentage',
                'value' => '0',
                'type' => 'float',
                'description' => 'Tax percentage',
                'group' => 'business',
                'is_public' => false,
            ],
            [
                'key' => 'currency',
                'value' => 'IDR',
                'type' => 'string',
                'description' => 'Default currency',
                'group' => 'business',
                'is_public' => true,
            ],
            [
                'key' => 'currency_symbol',
                'value' => 'Rp',
                'type' => 'string',
                'description' => 'Currency symbol',
                'group' => 'business',
                'is_public' => true,
            ],

            // Payment Settings
            [
                'key' => 'midtrans_server_key',
                'value' => '',
                'type' => 'string',
                'description' => 'Midtrans server key',
                'group' => 'payment',
                'is_public' => false,
            ],
            [
                'key' => 'midtrans_client_key',
                'value' => '',
                'type' => 'string',
                'description' => 'Midtrans client key',
                'group' => 'payment',
                'is_public' => true,
            ],
            [
                'key' => 'midtrans_is_production',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Midtrans production mode',
                'group' => 'payment',
                'is_public' => false,
            ],

            // Diggie API Settings
            [
                'key' => 'diggie_api_key',
                'value' => '',
                'type' => 'string',
                'description' => 'Diggie API key',
                'group' => 'diggie',
                'is_public' => false,
            ],
            [
                'key' => 'diggie_api_url',
                'value' => 'https://api.diggie.id',
                'type' => 'string',
                'description' => 'Diggie API base URL',
                'group' => 'diggie',
                'is_public' => false,
            ],
            [
                'key' => 'diggie_webhook_secret',
                'value' => '',
                'type' => 'string',
                'description' => 'Diggie webhook secret',
                'group' => 'diggie',
                'is_public' => false,
            ],
            [
                'key' => 'diggie_auto_sync',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Auto sync products from Diggie',
                'group' => 'diggie',
                'is_public' => false,
            ],

            // Social Media
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/reynrastore',
                'type' => 'string',
                'description' => 'Instagram URL',
                'group' => 'social',
                'is_public' => true,
            ],
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/reynrastore',
                'type' => 'string',
                'description' => 'Facebook URL',
                'group' => 'social',
                'is_public' => true,
            ],
            [
                'key' => 'social_twitter',
                'value' => 'https://twitter.com/reynrastore',
                'type' => 'string',
                'description' => 'Twitter URL',
                'group' => 'social',
                'is_public' => true,
            ],
            [
                'key' => 'social_youtube',
                'value' => 'https://youtube.com/@reynrastore',
                'type' => 'string',
                'description' => 'YouTube URL',
                'group' => 'social',
                'is_public' => true,
            ],

            // SEO Settings
            [
                'key' => 'meta_keywords',
                'value' => 'top up game, mobile legends, free fire, pubg mobile, genshin impact, valorant',
                'type' => 'string',
                'description' => 'Meta keywords',
                'group' => 'seo',
                'is_public' => true,
            ],
            [
                'key' => 'meta_author',
                'value' => 'Reynra Store',
                'type' => 'string',
                'description' => 'Meta author',
                'group' => 'seo',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
