<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Cache duration constants
     */
    const CACHE_DURATION_SHORT = 300; // 5 minutes
    const CACHE_DURATION_MEDIUM = 1800; // 30 minutes
    const CACHE_DURATION_LONG = 3600; // 1 hour
    const CACHE_DURATION_DAILY = 86400; // 24 hours

    /**
     * Get cached data or execute callback and cache result
     */
    public static function remember(string $key, int $duration, callable $callback)
    {
        try {
            return Cache::remember($key, $duration, $callback);
        } catch (\Exception $e) {
            Log::error("Cache error for key {$key}: " . $e->getMessage());
            return $callback();
        }
    }

    /**
     * Clear cache by pattern
     */
    public static function clearByPattern(string $pattern): void
    {
        try {
            $keys = Cache::getRedis()->keys($pattern);
            if (!empty($keys)) {
                Cache::getRedis()->del($keys);
            }
        } catch (\Exception $e) {
            Log::error("Cache clear error for pattern {$pattern}: " . $e->getMessage());
        }
    }

    /**
     * Clear all application cache
     */
    public static function clearAll(): void
    {
        try {
            Cache::flush();
        } catch (\Exception $e) {
            Log::error("Cache flush error: " . $e->getMessage());
        }
    }

    /**
     * Cache keys for different data types
     */
    public static function getCacheKey(string $type, ...$params): string
    {
        $key = "reynra_store:{$type}";
        
        if (!empty($params)) {
            $key .= ':' . implode(':', $params);
        }
        
        return $key;
    }

    /**
     * Get categories cache key
     */
    public static function getCategoriesKey(): string
    {
        return self::getCacheKey('categories', 'all');
    }

    /**
     * Get products cache key
     */
    public static function getProductsKey(array $filters = []): string
    {
        $filterString = !empty($filters) ? md5(serialize($filters)) : 'all';
        return self::getCacheKey('products', $filterString);
    }

    /**
     * Get product detail cache key
     */
    public static function getProductKey($productId): string
    {
        return self::getCacheKey('product', $productId);
    }

    /**
     * Get settings cache key
     */
    public static function getSettingsKey(string $group = 'all'): string
    {
        return self::getCacheKey('settings', $group);
    }

    /**
     * Get dashboard stats cache key
     */
    public static function getDashboardStatsKey(): string
    {
        return self::getCacheKey('dashboard', 'stats');
    }
}
