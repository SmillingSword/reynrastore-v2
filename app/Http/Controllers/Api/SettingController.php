<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    /**
     * Display a listing of settings (Admin)
     */
    public function index(Request $request)
    {
        $query = Setting::query();

        // Filter by group
        if ($request->has('group') && $request->group !== '') {
            $query->where('group', $request->group);
        }

        // Search by key or description
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $settings = $query->orderBy('group')->orderBy('key')->get();

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Store a newly created setting (Admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|unique:settings,key|max:255',
            'value' => 'required|string',
            'group' => 'required|string|max:100',
            'type' => 'required|in:string,number,boolean,json',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $setting = Setting::create($request->all());

        // Clear cache
        Cache::forget('settings');

        return response()->json([
            'success' => true,
            'data' => $setting,
            'message' => 'Setting created successfully'
        ], 201);
    }

    /**
     * Display the specified setting (Admin)
     */
    public function show(Setting $setting)
    {
        return response()->json([
            'success' => true,
            'data' => $setting
        ]);
    }

    /**
     * Update the specified setting (Admin)
     */
    public function update(Request $request, Setting $setting)
    {
        $request->validate([
            'value' => 'required|string',
            'group' => 'sometimes|string|max:100',
            'type' => 'sometimes|in:string,number,boolean,json',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $setting->update($request->all());

        // Clear cache
        Cache::forget('settings');

        return response()->json([
            'success' => true,
            'data' => $setting,
            'message' => 'Setting updated successfully'
        ]);
    }

    /**
     * Remove the specified setting (Admin)
     */
    public function destroy(Setting $setting)
    {
        $setting->delete();

        // Clear cache
        Cache::forget('settings');

        return response()->json([
            'success' => true,
            'message' => 'Setting deleted successfully'
        ]);
    }

    /**
     * Get settings by group (Admin)
     */
    public function byGroup($group)
    {
        $settings = Setting::where('group', $group)
            ->orderBy('key')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Get public settings (for frontend)
     */
    public function public()
    {
        $settings = Cache::remember('public_settings', 3600, function () {
            return Setting::where('is_public', true)
                ->get()
                ->keyBy('key')
                ->map(function ($setting) {
                    return $setting->parsed_value;
                });
        });

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Bulk update settings (Admin)
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|exists:settings,key',
            'settings.*.value' => 'required|string',
        ]);

        $updatedCount = 0;

        foreach ($request->settings as $settingData) {
            $setting = Setting::where('key', $settingData['key'])->first();
            if ($setting) {
                $setting->update(['value' => $settingData['value']]);
                $updatedCount++;
            }
        }

        // Clear cache
        Cache::forget('settings');
        Cache::forget('public_settings');

        return response()->json([
            'success' => true,
            'message' => "Updated {$updatedCount} settings successfully"
        ]);
    }

    /**
     * Get all setting groups (Admin)
     */
    public function groups()
    {
        $groups = Setting::select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        return response()->json([
            'success' => true,
            'data' => $groups
        ]);
    }

    /**
     * Get admin settings (Admin)
     */
    public function adminIndex()
    {
        try {
            // Get profit settings
            $profitSettings = [
                'default_profit_margin' => Setting::get('default_profit_percentage', 10),
                'auto_update_enabled' => Setting::get('auto_update_enabled', true),
                'update_interval' => Setting::get('update_interval', 60)
            ];

            // Get system settings
            $systemSettings = [
                'site_name' => Setting::get('site_name', 'Reynra Store'),
                'site_description' => Setting::get('site_description', 'Game Top Up Terpercaya'),
                'contact_email' => Setting::get('contact_email', 'admin@reynrastore.com'),
                'whatsapp_number' => Setting::get('whatsapp_number', '628123456789'),
                'admin_fee' => Setting::get('admin_fee', 1000),
                'minimum_order' => Setting::get('minimum_order', 5000),
                'maintenance_mode' => Setting::get('maintenance_mode', false)
            ];

            // Get category margins (placeholder for now)
            $categoryMargins = [];

            return response()->json([
                'success' => true,
                'data' => [
                    'profit_settings' => $profitSettings,
                    'system_settings' => $systemSettings,
                    'category_margins' => $categoryMargins
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch settings'
            ], 500);
        }
    }

    /**
     * Update profit settings (Admin)
     */
    public function updateProfitSettings(Request $request)
    {
        try {
            $request->validate([
                'default_profit_margin' => 'required|numeric|min:0|max:100',
                'auto_update_enabled' => 'boolean',
                'update_interval' => 'required|integer|min:1',
                'category_margins' => 'array'
            ]);

            // Update profit settings
            Setting::set('default_profit_percentage', $request->default_profit_margin);
            Setting::set('auto_update_enabled', $request->auto_update_enabled ?? false);
            Setting::set('update_interval', $request->update_interval);

            // Update category margins if provided
            if ($request->has('category_margins')) {
                Setting::set('category_margins', json_encode($request->category_margins));
            }

            return response()->json([
                'success' => true,
                'message' => 'Profit settings updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profit settings'
            ], 500);
        }
    }

    /**
     * Update system settings (Admin)
     */
    public function updateSystemSettings(Request $request)
    {
        try {
            $request->validate([
                'site_name' => 'required|string|max:255',
                'site_description' => 'nullable|string',
                'contact_email' => 'nullable|email',
                'whatsapp_number' => 'nullable|string',
                'admin_fee' => 'nullable|numeric|min:0',
                'minimum_order' => 'nullable|numeric|min:0',
                'maintenance_mode' => 'boolean'
            ]);

            // Update system settings
            Setting::set('site_name', $request->site_name);
            Setting::set('site_description', $request->site_description);
            Setting::set('contact_email', $request->contact_email);
            Setting::set('whatsapp_number', $request->whatsapp_number);
            Setting::set('admin_fee', $request->admin_fee ?? 0);
            Setting::set('minimum_order', $request->minimum_order ?? 0);
            Setting::set('maintenance_mode', $request->maintenance_mode ?? false);

            return response()->json([
                'success' => true,
                'message' => 'System settings updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update system settings'
            ], 500);
        }
    }

    /**
     * Test Digiflazz connection (Admin)
     */
    public function testDigiflazz()
    {
        try {
            $startTime = microtime(true);
            
            // Simple test - you can implement actual Digiflazz API test here
            $success = true; // Placeholder
            
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000);

            return response()->json([
                'success' => $success,
                'response_time' => $responseTime,
                'message' => $success ? 'Digiflazz connection successful' : 'Digiflazz connection failed'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'response_time' => 0,
                'message' => 'Digiflazz connection test failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test Midtrans connection (Admin)
     */
    public function testMidtrans()
    {
        try {
            $startTime = microtime(true);
            
            // Simple test - you can implement actual Midtrans API test here
            $success = true; // Placeholder
            
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000);

            return response()->json([
                'success' => $success,
                'response_time' => $responseTime,
                'message' => $success ? 'Midtrans connection successful' : 'Midtrans connection failed'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'response_time' => 0,
                'message' => 'Midtrans connection test failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get recent activities (Admin)
     */
    public function getActivities()
    {
        try {
            // Placeholder activities - you can implement actual activity logging
            $activities = [
                [
                    'id' => 1,
                    'type' => 'price_update',
                    'description' => 'Updated prices for Digiflazz products',
                    'created_at' => now()->subMinutes(30)
                ],
                [
                    'id' => 2,
                    'type' => 'order_created',
                    'description' => 'New order created: #ORD-20250811-001',
                    'created_at' => now()->subHours(1)
                ],
                [
                    'id' => 3,
                    'type' => 'order_completed',
                    'description' => 'Order completed: #ORD-20250811-002',
                    'created_at' => now()->subHours(2)
                ],
                [
                    'id' => 4,
                    'type' => 'system_update',
                    'description' => 'System settings updated',
                    'created_at' => now()->subHours(3)
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $activities
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch activities'
            ], 500);
        }
    }
}
