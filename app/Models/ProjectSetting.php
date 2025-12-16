<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectSetting extends Model
{
    protected $fillable = ['project_id', 'key', 'value'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public static function get($projectId, $key, $default = null)
    {
        $setting = self::where('project_id', $projectId)->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($projectId, $key, $value)
    {
        return self::updateOrCreate(
            ['project_id' => $projectId, 'key' => $key],
            ['value' => $value]
        );
    }
}

