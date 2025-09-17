<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DiggieService;
use Illuminate\Support\Facades\Log;

class UpdateDiggiePrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diggie:update-prices {--force : Force update even if recently updated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update product prices from Diggie API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Diggie price update...');
        
        try {
            $diggieService = app(DiggieService::class);
            
            // Check if force update or enough time has passed
            $lastUpdate = cache('diggie_last_update');
            $force = $this->option('force');
            
            if (!$force && $lastUpdate && now()->diffInMinutes($lastUpdate) < 30) {
                $this->warn('Price update was run recently. Use --force to override.');
                return Command::SUCCESS;
            }
            
            $this->info('Fetching latest prices from Diggie API...');
            $updatedCount = $diggieService->updateProductPrices();
            
            // Cache the last update time
            cache(['diggie_last_update' => now()], now()->addHour());
            
            $this->info("Successfully updated {$updatedCount} product prices.");
            
            // Log the successful update
            Log::info('Diggie prices updated successfully', [
                'updated_count' => $updatedCount,
                'timestamp' => now()
            ]);
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Failed to update prices: ' . $e->getMessage());
            
            Log::error('Diggie price update failed', [
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);
            
            return Command::FAILURE;
        }
    }
}
