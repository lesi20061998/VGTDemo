<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model để lưu settings trong project database
 * Sử dụng connection 'project' cố định
 */
class ProjectSettingModel extends Model
{
    protected $connection = 'project';

    protected $table = 'settings';

    protected $fillable = ['tenant_id', 'key', 'payload', 'group', 'locked'];

    protected $casts = [
        'payload' => 'array',
        'locked' => 'boolean',
    ];

    public static function set($key, $value, $group = 'general')
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['payload' => is_array($value) ? $value : ['value' => $value], 'group' => $group]
        );
    }

    public static function getValue($key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        $value = $setting->payload;

        if (is_array($value) && isset($value['value'])) {
            return $value['value'];
        }

        return $value ?? $default;
    }
}
