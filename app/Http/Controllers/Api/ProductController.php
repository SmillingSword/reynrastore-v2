<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;
use App\Models\PriceList;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'priceLists' => function ($q) {
            $q->where('is_active', true)
              ->where('seller_status', true)
              ->where('buyer_status', true)
              ->orderBy('selling_price', 'asc');
        }]);

        // Filter by active status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by category slug
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by product type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by featured
        if ($request->has('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Price range filter - now based on minimum price list price
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('priceLists', function ($q) use ($request) {
                $q->where('is_active', true)
                  ->where('seller_status', true)
                  ->where('buyer_status', true);
                
                if ($request->filled('min_price')) {
                    $q->where('selling_price', '>=', $request->min_price);
                }
                if ($request->filled('max_price')) {
                    $q->where('selling_price', '<=', $request->max_price);
                }
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if ($sortBy === 'price') {
            // Sort by minimum price list price
            $query->leftJoin('price_lists', function ($join) {
                $join->on('products.id', '=', 'price_lists.product_id')
                     ->where('price_lists.is_active', true)
                     ->where('price_lists.seller_status', true)
                     ->where('price_lists.buyer_status', true);
            })
            ->select('products.*')
            ->groupBy('products.id')
            ->orderByRaw('MIN(price_lists.selling_price) ' . $sortOrder);
        } elseif ($sortBy === 'name') {
            $query->orderBy('name', $sortOrder);
        } elseif ($sortBy === 'created_at') {
            $query->orderBy('created_at', $sortOrder);
        } else {
            $query->ordered();
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        // Transform the data to include price list info
        $products->getCollection()->transform(function ($product) {
            $activePriceLists = $product->priceLists->where('is_active', true)
                                                   ->where('seller_status', true)
                                                   ->where('buyer_status', true);
            
            $product->min_price = $activePriceLists->min('selling_price') ?? $product->selling_price;
            $product->max_price = $activePriceLists->max('selling_price') ?? $product->selling_price;
            $product->price_list_count = $activePriceLists->count();
            
            // Don't include the full price lists in the response to keep it lightweight
            unset($product->priceLists);
            
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'string|max:255',
            'base_price' => 'required|numeric|min:0',
            'profit_percentage' => 'nullable|numeric|min:0|max:100',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'type' => 'required|in:diggie,manual',
            'diggie_product_id' => 'nullable|string|max:255',
            'diggie_data' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'stock' => 'nullable|integer|min:0',
            'sort_order' => 'integer|min:0',
            'form_fields' => 'nullable|array',
            'instructions' => 'nullable|string',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Ensure slug is unique
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Product::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Calculate selling price if profit percentage is provided
        if (isset($validated['profit_percentage']) && $validated['profit_percentage'] > 0) {
            $validated['selling_price'] = $validated['base_price'] * (1 + ($validated['profit_percentage'] / 100));
        } else {
            // Use default profit percentage from settings
            $defaultProfit = Setting::get('default_profit_percentage', 10);
            $validated['profit_percentage'] = $defaultProfit;
            $validated['selling_price'] = $validated['base_price'] * (1 + ($defaultProfit / 100));
        }

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product->load('category'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::with('category')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($product->id),
            ],
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'string|max:255',
            'base_price' => 'required|numeric|min:0',
            'profit_percentage' => 'nullable|numeric|min:0|max:100',
            'sku' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],
            'type' => 'required|in:diggie,manual',
            'diggie_product_id' => 'nullable|string|max:255',
            'diggie_data' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'stock' => 'nullable|integer|min:0',
            'sort_order' => 'integer|min:0',
            'form_fields' => 'nullable|array',
            'instructions' => 'nullable|string',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Ensure slug is unique (excluding current product)
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Product::where('slug', $validated['slug'])->where('id', '!=', $product->id)->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Recalculate selling price if base price or profit percentage changed
        if (isset($validated['base_price']) || isset($validated['profit_percentage'])) {
            $basePrice = $validated['base_price'] ?? $product->base_price;
            $profitPercentage = $validated['profit_percentage'] ?? $product->profit_percentage;
            
            $validated['selling_price'] = $basePrice * (1 + ($profitPercentage / 100));
        }

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product->load('category'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        // Check if product has orders
        if ($product->orderItems()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete product with existing orders',
            ], 422);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    /**
     * Get featured products.
     */
    public function featured(): JsonResponse
    {
        $products = Product::with('category')
            ->active()
            ->featured()
            ->ordered()
            ->take(8)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Update product prices based on profit percentage.
     */
    public function updatePrices(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'profit_percentage' => 'required|numeric|min:0|max:100',
            'type' => 'nullable|in:diggie,manual',
        ]);

        $query = Product::query();

        // Filter by product IDs if provided
        if (!empty($validated['product_ids'])) {
            $query->whereIn('id', $validated['product_ids']);
        }

        // Filter by type if provided
        if (!empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        $products = $query->get();
        $updatedCount = 0;

        foreach ($products as $product) {
            $product->update([
                'profit_percentage' => $validated['profit_percentage'],
                'selling_price' => $product->base_price * (1 + ($validated['profit_percentage'] / 100)),
            ]);
            $updatedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Updated prices for {$updatedCount} products",
            'data' => [
                'updated_count' => $updatedCount,
                'profit_percentage' => $validated['profit_percentage'],
            ],
        ]);
    }

    /**
     * Get product by slug.
     */
    public function bySlug(string $slug): JsonResponse
    {
        $product = Product::with('category')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    /**
     * Toggle product status.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Product status updated successfully',
            'data' => $product->load('category'),
        ]);
    }

    /**
     * Sync products from Digiflazz.
     */
    public function syncDigiflazz(): JsonResponse
    {
        try {
            // Run the artisan command to sync Diggie products
            Artisan::call('diggie:sync-products');
            $output = Artisan::output();
            
            // Get updated product count
            $totalProducts = Product::count();
            $diggieProducts = Product::where('type', 'diggie')->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Sinkronisasi Digiflazz berhasil',
                'data' => [
                    'total_products' => $totalProducts,
                    'diggie_products' => $diggieProducts,
                    'output' => $output,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal sinkronisasi Digiflazz: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get price lists for a product.
     */
    public function getPriceLists(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        
        $priceLists = PriceList::where('product_id', $id)
            ->orderBy('selling_price', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $product,
                'price_lists' => $priceLists,
            ],
        ]);
    }

    /**
     * Store a new price list for a product.
     */
    public function storePriceList(Request $request, string $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        // Only allow manual products to add price lists
        if ($product->type !== 'manual') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya produk manual yang bisa menambah price list',
            ], 422);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'profit_percentage' => 'nullable|numeric|min:0|max:100',
            'stock' => 'nullable|integer|min:0',
            'unlimited_stock' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Calculate selling price
        $profitPercentage = $validated['profit_percentage'] ?? $product->profit_percentage ?? 15;
        $validated['selling_price'] = $validated['base_price'] * (1 + ($profitPercentage / 100));
        $validated['profit_percentage'] = $profitPercentage;
        $validated['product_id'] = $id;
        $validated['type'] = 'manual';
        $validated['seller_status'] = true;
        $validated['buyer_status'] = true;
        $validated['diggie_sku_code'] = null; // Manual products don't have diggie_sku_code

        $priceList = PriceList::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Price list berhasil ditambahkan',
            'data' => $priceList,
        ], 201);
    }

    /**
     * Update a price list.
     */
    public function updatePriceList(Request $request, string $productId, string $priceListId): JsonResponse
    {
        $product = Product::findOrFail($productId);
        $priceList = PriceList::where('product_id', $productId)->findOrFail($priceListId);

        // Only allow manual products to update price lists
        if ($product->type !== 'manual') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya produk manual yang bisa mengubah price list',
            ], 422);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'profit_percentage' => 'nullable|numeric|min:0|max:100',
            'stock' => 'nullable|integer|min:0',
            'unlimited_stock' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Recalculate selling price
        $profitPercentage = $validated['profit_percentage'] ?? $priceList->profit_percentage;
        $validated['selling_price'] = $validated['base_price'] * (1 + ($profitPercentage / 100));
        $validated['profit_percentage'] = $profitPercentage;
        
        // Ensure diggie_sku_code is set for manual products
        if (!isset($validated['diggie_sku_code'])) {
            $validated['diggie_sku_code'] = null;
        }

        $priceList->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Price list berhasil diupdate',
            'data' => $priceList,
        ]);
    }

    /**
     * Delete a price list.
     */
    public function deletePriceList(string $productId, string $priceListId): JsonResponse
    {
        $product = Product::findOrFail($productId);
        $priceList = PriceList::where('product_id', $productId)->findOrFail($priceListId);

        // Only allow manual products to delete price lists
        if ($product->type !== 'manual') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya produk manual yang bisa menghapus price list',
            ], 422);
        }

        $priceList->delete();

        return response()->json([
            'success' => true,
            'message' => 'Price list berhasil dihapus',
        ]);
    }

    /**
     * Update product image.
     */
    public function updateImage(Request $request, string $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'image' => 'required|file|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        try {
            // Delete old image if exists
            if ($product->image && !str_starts_with($product->image, 'http') && !str_starts_with($product->image, 'data:')) {
                $oldImagePath = public_path('storage/' . $product->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Generate unique filename
            $file = $validated['image'];
            $filename = time() . '_' . $product->id . '.' . $file->getClientOriginalExtension();
            
            // Store in public/storage/products directory
            $path = $file->storeAs('products', $filename, 'public');

            // Update product with new image path
            $product->update(['image' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Product image updated successfully',
                'data' => $product->load('category'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage(),
            ], 500);
        }
    }
}
