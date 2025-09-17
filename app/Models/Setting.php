<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'group',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            Cache::forget('settings');
        });

        static::deleted(function () {
            Cache::forget('settings');
        });
    }

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null)
    {
        $settings = static::getAllCached();
        
        if (!isset($settings[$key])) {
            return $default;
        }

        $setting = $settings[$key];
        
        return static::castValue($setting['value'], $setting['type']);
    }

    /**
     * Alias for get method for backward compatibility.
     */
    public static function getValue(string $key, $default = null)
    {
        return static::get($key, $default);
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, $value, string $type = 'string', string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
                'group' => $group,
            ]
        );
    }

    /**
     * Get all settings cached.
     */
    public static function getAllCached(): array
    {
        return Cache::remember('settings', 3600, function () {
            return static::all()->keyBy('key')->toArray();
        });
    }

    /**
     * Get public settings (accessible by frontend).
     */
    public static function getPublicSettings(): array
    {
        $settings = static::getAllCached();
        
        return collect($settings)
            ->filter(function ($setting) {
                return $setting['is_public'];
            })
            ->mapWithKeys(function ($setting, $key) {
                return [$key => static::castValue($setting['value'], $setting['type'])];
            })
            ->toArray();
    }

    /**
     * Get settings by group.
     */
    public static function getByGroup(string $group): array
    {
        $settings = static::getAllCached();
        
        return collect($settings)
            ->filter(function ($setting) use ($group) {
                return $setting['group'] === $group;
            })
            ->mapWithKeys(function ($setting, $key) {
                return [$key => static::castValue($setting['value'], $setting['type'])];
            })
            ->toArray();
    }

    /**
     * Cast value based on type.
     */
    protected static function castValue($value, string $type)
    {
        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Scope a query to only include public settings.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to filter by group.
     */
    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}
