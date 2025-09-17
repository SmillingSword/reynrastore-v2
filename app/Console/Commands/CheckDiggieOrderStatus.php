<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DiggieService;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;

class CheckDiggieOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diggie:check-status {--limit=50 : Limit number of orders to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check status of pending Diggie orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking Diggie order status...');
        
        try {
            $diggieService = app(DiggieService::class);
            $limit = $this->option('limit');
            
            // Get pending Diggie orders
            $pendingOrders = OrderItem::whereHas('product', function ($query) {
                    $query->where('type', 'diggie');
                })
                ->whereIn('status', ['processing', 'pending'])
                ->whereNotNull('diggie_transaction_id')
                ->limit($limit)
                ->get();
            
            if ($pendingOrders->isEmpty()) {
                $this->info('No pending Diggie orders found.');
                return Command::SUCCESS;
            }
            
            $this->info("Found {$pendingOrders->count()} pending orders to check.");
            
            $completedCount = 0;
            $failedCount = 0;
            
            foreach ($pendingOrders as $orderItem) {
                try {
                    $this->line("Checking order item #{$orderItem->id} - Diggie ID: {$orderItem->diggie_transaction_id}");
                    
                    $status = $diggieService->checkOrderStatus($orderItem->diggie_transaction_id);
                    
                    // Update order item based on Diggie status
                    switch (strtolower($status['status'])) {
                        case 'success':
                        case 'completed':
                            $orderItem->update([
                                'status' => 'completed',
                                'completed_at' => now(),
                                'diggie_response' => $status,
                            ]);
                            $completedCount++;
                            $this->info("✓ Order item #{$orderItem->id} completed");
                            break;
                            
                        case 'failed':
                        case 'error':
                            $orderItem->update([
                                'status' => 'failed',
                                'error_message' => $status['message'] ?? 'Order failed',
                                'diggie_response' => $status,
                            ]);
                            $failedCount++;
                            $this->error("✗ Order item #{$orderItem->id} failed: " . ($status['message'] ?? 'Unknown error'));
                            break;
                            
                        case 'processing':
                        case 'pending':
                            // Keep as processing
                            $this->line("⏳ Order item #{$orderItem->id} still processing");
                            break;
                            
                        default:
                            $this->warn("? Order item #{$orderItem->id} has unknown status: " . $status['status']);
                            break;
                    }
                    
                    // Small delay to avoid rate limiting
                    usleep(200000); // 0.2 seconds
                    
                } catch (\Exception $e) {
                    $this->error("Failed to check order item #{$orderItem->id}: " . $e->getMessage());
                    Log::error('Failed to check Diggie order status', [
                        'order_item_id' => $orderItem->id,
                        'diggie_transaction_id' => $orderItem->diggie_transaction_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            $this->info("Status check completed:");
            $this->info("- Completed: {$completedCount}");
            $this->info("- Failed: {$failedCount}");
            $this->info("- Still processing: " . ($pendingOrders->count() - $completedCount - $failedCount));
            
            // Update parent orders if all items are completed
            $this->updateParentOrders();
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Failed to check order status: ' . $e->getMessage());
            
            Log::error('Diggie status check failed', [
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);
            
            return Command::FAILURE;
        }
    }
    
    /**
     * Update parent orders if all items are completed
     */
    private function updateParentOrders()
    {
        $this->info('Updating parent order statuses...');
        
        // Get orders that might need status updates
        $orders = \App\Models\Order::whereHas('orderItems', function ($query) {
                $query->whereIn('status', ['completed', 'failed']);
            })
            ->where('status', '!=', 'completed')
            ->get();
        
        $updatedOrders = 0;
        
        foreach ($orders as $order) {
            $totalItems = $order->orderItems->count();
            $completedItems = $order->orderItems->where('status', 'completed')->count();
            $failedItems = $order->orderItems->where('status', 'failed')->count();
            
            if ($completedItems === $totalItems) {
                // All items completed
                $order->update(['status' => 'completed']);
                $updatedOrders++;
                $this->info("✓ Order #{$order->order_number} marked as completed");
            } elseif ($failedItems === $totalItems) {
                // All items failed
                $order->update(['status' => 'failed']);
                $updatedOrders++;
                $this->info("✗ Order #{$order->order_number} marked as failed");
            } elseif (($completedItems + $failedItems) === $totalItems) {
                // Mixed results - mark as partially completed
                $order->update(['status' => 'partially_completed']);
                $updatedOrders++;
                $this->info("⚠ Order #{$order->order_number} marked as partially completed");
            }
        }
        
        $this->info("Updated {$updatedOrders} parent orders.");
    }
}
