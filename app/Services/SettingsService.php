<?php

namespace App\Services;

use App\Models\ProjectSettingModel;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingsService
{
    private static $instance = null;

    private $settings = [];

    private $loadedForProject = null;

    private function __construct()
    {
        // Don't load in constructor - load on demand
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Check if currently in project context
     */
    private function isProjectContext(): bool
    {
        return config('database.default') === 'project';
    }

    /**
     * Get current project identifier for cache key
     */
    private function getCurrentProjectKey(): string
    {
        if ($this->isProjectContext()) {
            return session('current_project') ?? config('database.connections.project.database') ?? 'project';
        }

        return 'main';
    }

    private function loadSettings()
    {
        $currentProject = $this->getCurrentProjectKey();

        // Skip if already loaded for this project
        if ($this->loadedForProject === $currentProject && ! empty($this->settings)) {
            return;
        }

        $this->settings = [];
        $this->loadedForProject = $currentProject;

        if ($this->isProjectContext()) {
            try {
                $this->settings = DB::connection('project')
                    ->table('settings')
                    ->pluck('payload', 'key')
                    ->map(fn ($payload) => json_decode($payload, true))
                    ->toArray();
            } catch (\Exception $e) {
                \Log::warning("Failed to load project settings for {$currentProject}: ".$e->getMessage());
                $this->settings = [];
            }
        } else {
            $cacheKey = 'all_settings_main';
            $this->settings = Cache::rememberForever($cacheKey, fn () => Setting::pluck('payload', 'key')->toArray());
        }
    }

    public function get($key, $default = null)
    {
        $this->loadSettings();

        if (! isset($this->settings[$key])) {
            return $default;
        }

        $value = $this->settings[$key];

        // Nếu là array và có key 'value', trả về giá trị đó
        if (\is_array($value)) {
            if (isset($value['value'])) {
                return $value['value'];
            }

            return $value;
        }

        return $value;
    }

    public function set($key, $value, $group = null, $locked = false)
    {
        if ($this->isProjectContext()) {
            ProjectSettingModel::set($key, $value, $group);
        } else {
            $tenantId = session('current_tenant_id');

            Setting::updateOrCreate(
                ['key' => $key, 'tenant_id' => $tenantId],
                [
                    'payload' => $value,
                    'group' => $group,
                    'locked' => $locked,
                ]
            );
        }

        $this->clearCache();
    }

    public function clearCache()
    {
        Cache::forget('all_settings_main');
        $this->settings = [];
        $this->loadedForProject = null;
    }

    public function forceReload()
    {
        $this->settings = [];
        $this->loadedForProject = null;
        $this->loadSettings();
    }
}
