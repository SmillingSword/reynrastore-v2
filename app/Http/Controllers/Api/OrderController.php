<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\DiggieService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of orders (Admin)
     */
    public function index(Request $request)
    {
        $query = Order::with(['orderItems.product'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status !== '') {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by order number or customer name
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'required|email|max:255',
                'customer_phone' => 'required|string|max:20',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.form_data' => 'required|array',
            ]);

            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'status' => 'pending',
                'payment_status' => 'pending',
                'subtotal' => 0,
                'total_amount' => 0,
            ]);

            $totalAmount = 0;

            // Create order items
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Validate form data against product requirements
                $this->validateFormData($product, $item['form_data']);
                
                // Get price from price_list if available
                $priceListId = $item['form_data']['price_list_id'] ?? null;
                $itemPrice = $product->selling_price;
                
                if ($priceListId) {
                    $priceList = \App\Models\PriceList::find($priceListId);
                    if ($priceList && $priceList->product_id == $product->id) {
                        $itemPrice = $priceList->selling_price;
                    }
                }
                
                $itemTotal = $itemPrice * $item['quantity'];
                $totalAmount += $itemTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'unit_price' => $itemPrice,
                    'quantity' => $item['quantity'],
                    'total_price' => $itemTotal,
                    'product_data' => [
                        'name' => $product->name,
                        'description' => $product->description,
                        'type' => $product->type,
                        'category' => $product->category?->name,
                        'price_list_id' => $priceListId,
                        'price_list_name' => $priceList?->name ?? null,
                    ],
                    'form_data' => $item['form_data'],
                    'status' => 'pending',
                ]);

                // Reduce stock for manual products
                if ($product->type === 'manual' && $product->stock !== null) {
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Insufficient stock for product: {$product->name}");
                    }
                    $product->decrement('stock', $item['quantity']);
                }
            }

            // Calculate payment fees
            $paymentMethod = $request->items[0]['form_data']['payment_method'] ?? 'qris';
            $paymentFee = $this->calculatePaymentFee($totalAmount, $paymentMethod);
            $finalTotal = $totalAmount + $paymentFee;

            // Update order with payment details
            $order->update([
                'subtotal' => $totalAmount,
                'total_amount' => $finalTotal,
                'payment_method' => $paymentMethod,
                'payment_data' => [
                    'payment_fee' => $paymentFee,
                    'subtotal_amount' => $totalAmount,
                    'fee_calculation' => [
                        'method' => $paymentMethod,
                        'fee_amount' => $paymentFee
                    ]
                ],
                'customer_data' => [
                    'name' => $request->customer_name,
                    'email' => $request->customer_email,
                    'phone' => $request->customer_phone,
                    'form_data' => $request->items[0]['form_data'] ?? []
                ]
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $order->load('orderItems.product'),
                'message' => 'Order created successfully'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        return response()->json([
            'success' => true,
            'data' => $order->load(['orderItems.product.category'])
        ]);
    }

    /**
     * Update the specified order (Admin)
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'sometimes|in:pending,processing,completed,failed,cancelled',
            'payment_status' => 'sometimes|in:pending,paid,failed,expired,cancelled',
            'notes' => 'sometimes|string|nullable',
        ]);

        $order->update($request->only(['status', 'payment_status', 'notes']));

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order updated successfully'
        ]);
    }

    /**
     * Remove the specified order (Admin)
     */
    public function destroy(Order $order)
    {
        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete paid order'
            ], 400);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully'
        ]);
    }

    /**
     * Track order by order number
     */
    public function track($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['orderItems.product'])
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Process order items (Admin)
     */
    public function processItems(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);
            
            if ($order->payment_status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order must be paid before processing'
                ], 400);
            }

            $processedCount = 0;
            $diggieService = app(DiggieService::class);

            foreach ($order->orderItems as $item) {
                if ($item->status === 'pending') {
                    if ($item->product->type === 'diggie') {
                        try {
                            $diggieService->processOrder($item);
                            $processedCount++;
                        } catch (\Exception $e) {
                            Log::error('Failed to process Diggie order item: ' . $e->getMessage());
                        }
                    } else {
                        // Manual processing
                        $item->update([
                            'status' => 'pending_manual',
                            'processed_at' => now(),
                        ]);
                        $processedCount++;
                    }
                }
            }

            if ($processedCount > 0) {
                $order->update(['status' => 'processing']);
            }

            return response()->json([
                'success' => true,
                'message' => "Processed {$processedCount} items successfully"
            ]);

        } catch (\Exception $e) {
            Log::error('Order processing error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process order items'
            ], 500);
        }
    }

    /**
     * Update order status (Admin)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,failed,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order status updated successfully'
        ]);
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber()
    {
        do {
            $orderNumber = 'RS' . date('Ymd') . strtoupper(Str::random(6));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Get orders for admin panel.
     */
    public function adminIndex(Request $request)
    {
        try {
            $query = Order::with(['orderItems.product']);

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Search by order number or customer
            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('order_number', 'like', '%' . $request->search . '%')
                      ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                      ->orWhere('customer_email', 'like', '%' . $request->search . '%');
                });
            }

            $orders = $query->orderBy('created_at', 'desc')->get();

            // Add is_manual field based on customer_data
            $orders = $orders->map(function ($order) {
                // Handle customer_data safely
                $customerData = null;
                if (is_string($order->customer_data)) {
                    $customerData = json_decode($order->customer_data, true);
                } elseif (is_array($order->customer_data)) {
                    $customerData = $order->customer_data;
                }
                
                $order->is_manual = isset($customerData['type']) && $customerData['type'] === 'manual';
                return $order;
            });

            return response()->json([
                'success' => true,
                'data' => $orders,
            ]);

        } catch (\Exception $e) {
            Log::error('Admin orders fetch error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders',
                'data' => []
            ], 500);
        }
    }

    /**
     * Process order manually.
     */
    public function processOrder($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be processed',
            ], 422);
        }

        $order->update(['status' => 'processing']);

        return response()->json([
            'success' => true,
            'message' => 'Order is now being processed',
            'data' => $order,
        ]);
    }

    /**
     * Complete order.
     */
    public function completeOrder($id)
    {
        $order = Order::findOrFail($id);

        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be completed',
            ], 422);
        }

        $order->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order completed successfully',
            'data' => $order,
        ]);
    }

    /**
     * Cancel order.
     */
    public function cancelOrder($id)
    {
        $order = Order::findOrFail($id);

        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled',
            ], 422);
        }

        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => $order,
        ]);
    }

    /**
     * Get dashboard statistics
     */
    public function dashboardStats()
    {
        try {
            // Get total orders
            $totalOrders = Order::count();
            
            // Get total revenue (only from completed and paid orders)
            $totalRevenue = Order::where('status', 'completed')
                ->where('payment_status', 'paid')
                ->sum('total_amount');
            
            // Get pending orders
            $pendingOrders = Order::where('status', 'pending')->count();
            
            // Get active products
            $activeProducts = Product::where('is_active', true)->count();
            
            // Get diggie products
            $diggieProducts = Product::where('type', 'diggie')
                ->where('is_active', true)
                ->count();
            
            // Get manual products
            $manualProducts = Product::where('type', 'manual')
                ->where('is_active', true)
                ->count();
            
            // Get revenue for last 7 days
            $revenueChart = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dayRevenue = Order::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->where('payment_status', 'paid')
                    ->sum('total_amount');
                
                $revenueChart[] = [
                    'day' => $date->format('D'),
                    'date' => $date->format('Y-m-d'),
                    'amount' => (float) $dayRevenue
                ];
            }
            
            // Get recent orders (last 10)
            $recentOrders = Order::with(['orderItems.product'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($order) {
                    $productName = $order->orderItems->first()?->product?->name ?? 'Unknown Product';
                    
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'product' => $productName,
                        'customer' => $order->customer_name,
                        'amount' => (float) $order->total_amount,
                        'status' => $order->status,
                        'payment_status' => $order->payment_status,
                        'time' => $order->created_at->diffForHumans(),
                        'created_at' => $order->created_at->format('Y-m-d H:i:s')
                    ];
                });
            
            // Get orders by status for additional stats
            $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
            
            // Get monthly revenue comparison
            $currentMonthRevenue = Order::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('status', 'completed')
                ->where('payment_status', 'paid')
                ->sum('total_amount');
                
            $lastMonthRevenue = Order::whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->where('status', 'completed')
                ->where('payment_status', 'paid')
                ->sum('total_amount');
            
            $revenueGrowth = $lastMonthRevenue > 0 
                ? (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
                : 0;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => [
                        'totalOrders' => $totalOrders,
                        'totalRevenue' => (float) $totalRevenue,
                        'pendingOrders' => $pendingOrders,
                        'activeProducts' => $activeProducts,
                        'diggieProducts' => $diggieProducts,
                        'manualProducts' => $manualProducts,
                        'revenueGrowth' => round($revenueGrowth, 1)
                    ],
                    'revenueChart' => $revenueChart,
                    'recentOrders' => $recentOrders,
                    'ordersByStatus' => $ordersByStatus
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Dashboard stats error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard statistics'
            ], 500);
        }
    }

    /**
     * Get recent orders for homepage slider.
     */
    public function recent(Request $request)
    {
        try {
            $limit = $request->get('limit', 6);
            
            // Get recent orders with proper relationships
            $orders = Order::with(['orderItems.product'])
                ->whereHas('orderItems')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($order) {
                    // Format data untuk frontend
                    $firstItem = $order->orderItems->first();
                    $product = $firstItem ? $firstItem->product : null;
                    
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'customer_name' => $order->customer_name,
                        'total_amount' => (float) $order->total_amount,
                        'status' => $order->status,
                        'payment_status' => $order->payment_status,
                        'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                        'time_ago' => $order->created_at->diffForHumans(),
                        'orderItems' => $order->orderItems->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'quantity' => $item->quantity,
                                'unit_price' => (float) $item->unit_price,
                                'total_price' => (float) $item->total_price,
                                'product' => $item->product ? [
                                    'id' => $item->product->id,
                                    'name' => $item->product->name,
                                    'slug' => $item->product->slug,
                                    'type' => $item->product->type,
                                    'selling_price' => (float) $item->product->selling_price,
                                ] : [
                                    'id' => null,
                                    'name' => $item->product_name ?? 'Unknown Product',
                                    'slug' => null,
                                    'type' => 'unknown',
                                    'selling_price' => (float) $item->unit_price,
                                ]
                            ];
                        })
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $orders,
                'message' => 'Recent orders fetched successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Recent orders fetch error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recent orders',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get activities/logs for admin dashboard
     */
    public function getActivities(Request $request)
    {
        try {
            $limit = $request->get('limit', 20);
            
            // Get recent activities from orders
            $activities = Order::with(['orderItems.product'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($order) {
                    $activity = [
                        'id' => $order->id,
                        'type' => 'order',
                        'title' => 'Order ' . $order->order_number,
                        'description' => 'Order dari ' . $order->customer_name,
                        'status' => $order->status,
                        'payment_status' => $order->payment_status,
                        'amount' => (float) $order->total_amount,
                        'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                        'time_ago' => $order->created_at->diffForHumans(),
                        'icon' => $this->getActivityIcon($order->status),
                        'color' => $this->getActivityColor($order->status),
                    ];
                    
                    return $activity;
                });

            return response()->json([
                'success' => true,
                'data' => $activities,
                'message' => 'Activities fetched successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Activities fetch error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch activities',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get activity icon based on status
     */
    private function getActivityIcon($status)
    {
        $icons = [
            'pending' => 'clock',
            'processing' => 'refresh',
            'completed' => 'check-circle',
            'failed' => 'x-circle',
            'cancelled' => 'x-circle',
        ];

        return $icons[$status] ?? 'circle';
    }

    /**
     * Get activity color based on status
     */
    private function getActivityColor($status)
    {
        $colors = [
            'pending' => 'yellow',
            'processing' => 'blue',
            'completed' => 'green',
            'failed' => 'red',
            'cancelled' => 'gray',
        ];

        return $colors[$status] ?? 'gray';
    }

    /**
     * Calculate payment fee based on method and amount
     */
    private function calculatePaymentFee($amount, $paymentMethod)
    {
        $paymentFees = [
            'qris' => ['type' => 'percentage', 'value' => 0.7],
            // No fees for manual payment methods
            'bank_transfer' => ['type' => 'fixed', 'value' => 0],
            'bca' => ['type' => 'fixed', 'value' => 0],
            'seabank' => ['type' => 'fixed', 'value' => 0],
            'ewallet' => ['type' => 'fixed', 'value' => 0],
            'dana' => ['type' => 'fixed', 'value' => 0],
            'ovo' => ['type' => 'fixed', 'value' => 0],
            'gopay' => ['type' => 'fixed', 'value' => 0],
            'shopeepay' => ['type' => 'fixed', 'value' => 0]
        ];

        $fee = $paymentFees[$paymentMethod] ?? $paymentFees['qris'];
        
        if ($fee['type'] === 'percentage') {
            return round($amount * ($fee['value'] / 100));
        } else {
            return $fee['value'];
        }
    }

    /**
     * Validate form data against product requirements
     */
    private function validateFormData(Product $product, array $formData)
    {
        $formFields = $product->form_fields;
        
        if (!$formFields) {
            return;
        }

        // Map common field names to our form data structure
        $fieldMapping = [
            'user_id' => 'game_id',
            'game_id' => 'game_id',
            'phone' => 'game_id',
            'phone_number' => 'game_id',
            'account_id' => 'game_id',
            'email' => 'game_id',
            'meter_number' => 'game_id',
            'customer_id' => 'game_id',
            'card_number' => 'game_id',
        ];

        foreach ($formFields as $field) {
            $fieldName = $field['name'];
            $isRequired = $field['required'] ?? false;
            
            if ($isRequired) {
                // Check if we have the field directly
                $hasValue = isset($formData[$fieldName]) && !empty($formData[$fieldName]);
                
                // If not found, check mapped field names
                if (!$hasValue && isset($fieldMapping[strtolower($fieldName)])) {
                    $mappedField = $fieldMapping[strtolower($fieldName)];
                    $hasValue = isset($formData[$mappedField]) && !empty($formData[$mappedField]);
                }
                
                // If still not found, check if it's a common field that should be mapped to game_id
                if (!$hasValue && in_array(strtolower($fieldName), ['user_id', 'phone', 'email', 'account'])) {
                    $hasValue = isset($formData['game_id']) && !empty($formData['game_id']);
                }
                
                if (!$hasValue) {
                    throw new \Exception("Field '{$field['label']}' is required for product: {$product->name}");
                }
            }
        }
    }

    /**
     * Confirm payment for manual payment methods (Admin)
     */
    public function confirmPayment(Request $request, $id)
    {
        try {
            $request->validate([
                'notes' => 'nullable|string|max:500'
            ]);

            $order = Order::findOrFail($id);

            // Check if order can be confirmed
            if ($order->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment already confirmed'
                ], 400);
            }

            if (!in_array($order->payment_method, ['bank_transfer', 'bca', 'seabank', 'ewallet', 'dana', 'ovo', 'gopay', 'shopeepay'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only manual payment methods can be confirmed'
                ], 400);
            }

            // Update order payment status
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'payment_confirmed_by' => 'admin', // You can get actual admin user if auth is implemented
                'payment_confirmed_at' => now(),
                'payment_confirmation_notes' => $request->notes
            ]);

            // Auto-process to DigiFlazz if product is diggie type
            $this->autoProcessToDigiFlazz($order);

            return response()->json([
                'success' => true,
                'message' => 'Payment confirmed successfully',
                'data' => $order->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error('Payment confirmation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm payment'
            ], 500);
        }
    }

    /**
     * Auto-process order to DigiFlazz after payment confirmation
     */
    private function autoProcessToDigiFlazz(Order $order)
    {
        try {
            $diggieService = app(DiggieService::class);
            $processedItems = 0;

            foreach ($order->orderItems as $item) {
                if ($item->product && $item->product->type === 'diggie' && $item->status === 'pending') {
                    try {
                        $diggieService->processOrder($item);
                        $processedItems++;
                        
                        Log::info("Auto-processed DigiFlazz order item: {$item->id}");
                    } catch (\Exception $e) {
                        Log::error("Failed to auto-process DigiFlazz order item {$item->id}: " . $e->getMessage());
                    }
                }
            }

            if ($processedItems > 0) {
                $order->update([
                    'auto_processed' => true,
                    'auto_processed_at' => now()
                ]);

                Log::info("Auto-processed {$processedItems} items for order {$order->order_number}");
            }

        } catch (\Exception $e) {
            Log::error("Auto-process to DigiFlazz failed for order {$order->order_number}: " . $e->getMessage());
        }
    }

    /**
     * Reject payment for manual payment methods (Admin)
     */
    public function rejectPayment(Request $request, $id)
    {
        try {
            $request->validate([
                'notes' => 'required|string|max:500'
            ]);

            $order = Order::findOrFail($id);

            // Check if order can be rejected
            if ($order->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot reject confirmed payment'
                ], 400);
            }

            // Update order payment status
            $order->update([
                'payment_status' => 'failed',
                'status' => 'failed',
                'payment_confirmed_by' => 'admin',
                'payment_confirmed_at' => now(),
                'payment_confirmation_notes' => $request->notes
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment rejected successfully',
                'data' => $order->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error('Payment rejection error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject payment'
            ], 500);
        }
    }
}
