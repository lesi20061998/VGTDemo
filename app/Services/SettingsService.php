<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    private static $instance = null;
    private $settings = [];

    private function __construct()
    {
        $this->loadSettings();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadSettings()
    {
        $tenantId = session('current_tenant_id');
        $cacheKey = 'all_settings_' . ($tenantId ?: 'default');
        
        $this->settings = Cache::rememberForever($cacheKey, function () use ($tenantId) {
            return Setting::withoutGlobalScope('tenant')
                ->where(function($query) use ($tenantId) {
                    $query->where('tenant_id', $tenantId)
                          ->orWhereNull('tenant_id');
                })
                ->pluck('payload', 'key')
                ->toArray();
        });
    }

    public function get($key, $default = null)
    {
        if (!isset($this->settings[$key])) {
            return $default;
        }
        
        $value = $this->settings[$key];
        
        // Nếu là array và có key 'value', trả về giá trị đó
        if (is_array($value)) {
            if (isset($value['value'])) {
                return $value['value'];
            }
            // Nếu là array nhưng không có key 'value', trả về default
            return $default;
        }
        
        return $value;
    }

    public function set($key, $value, $group = null, $locked = false)
    {
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'payload' => $value,
                'group' => $group,
                'locked' => $locked
            ]
        );

        $this->clearCache();
    }

    public function clearCache()
    {
        $tenantId = session('current_tenant_id');
        $cacheKey = 'all_settings_' . ($tenantId ?: 'default');
        Cache::forget($cacheKey);
        $this->loadSettings();
    }
}

