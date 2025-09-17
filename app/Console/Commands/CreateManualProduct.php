<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class CreateManualProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:create-manual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a manual Steam Wallet product';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $product = new Product();
        $product->name = 'Steam Wallet 100.000';
        $product->slug = 'steam-wallet-100000';
        $product->category_id = 6; // Steam Wallet category
        $product->description = 'Top up Steam Wallet Rp 100.000 dengan proses manual 1-24 jam. Kirimkan email Steam Anda setelah pembayaran.';
        $product->base_price = 95000;
        $product->profit_percentage = 15;
        $product->selling_price = 95000 * (1 + 15/100);
        $product->sku = 'STEAM-100K';
        $product->type = 'manual';
        $product->stock = 50;
        $product->is_active = true;
        $product->image = '/images/products/steam-wallet-100k.jpg';
        $product->form_fields = json_encode([
            [
                'name' => 'steam_email',
                'label' => 'Email Steam',
                'type' => 'email',
                'required' => true,
                'placeholder' => 'Masukkan email Steam Anda'
            ]
        ]);
        $product->instructions = 'Masukkan email Steam yang benar. Proses manual 1-24 jam setelah pembayaran berhasil.';

        $product->save();

        $this->info("Product created successfully!");
        $this->info("ID: " . $product->id);
        $this->info("Name: " . $product->name);
        $this->info("Price: Rp " . number_format($product->selling_price));
        $this->info("Type: " . $product->type);
        
        return 0;
    }
}
