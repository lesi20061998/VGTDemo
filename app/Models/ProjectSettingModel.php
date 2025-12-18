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

    protected $fillable = ['tenant_id', 'key', 'payload', 'value', 'group', 'locked', 'project_id', 'type', 'description'];

    protected $casts = [
        'payload' => 'array',
        'locked' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($setting) {
            if (! $setting->tenant_id && session('current_tenant_id')) {
                $setting->tenant_id = session('current_tenant_id');
            }

            if (! $setting->project_id) {
                $project = request()->attributes->get('project');
                if ($project) {
                    $setting->project_id = $project->id;
                }
            }
        });
    }

    public static function set($key, $value, $group = 'general')
    {
        $project = request()->attributes->get('project');
        $tenantId = session('current_tenant_id');

        // Chuẩn hóa giá trị để so sánh
        $normalizedValue = \is_array($value) ? $value : ['value' => $value];

        // Kiểm tra xem key đã tồn tại chưa
        $existingSetting = static::where('key', $key)->first();

        if ($existingSetting) {
            // Kiểm tra xem giá trị có thay đổi không
            $existingValue = $existingSetting->payload;
            
            // Nếu giá trị giống nhau thì không cần update
            if ($existingValue === $normalizedValue && $existingSetting->group === $group) {
                return $existingSetting;
            }

            // Nếu có thay đổi thì update
            $existingSetting->update([
                'payload' => $normalizedValue,
                'group' => $group,
                'updated_at' => now(),
            ]);

            return $existingSetting;
        }

        // Nếu chưa có thì dùng INSERT IGNORE để tránh duplicate key error
        $data = [
            'key' => $key,
            'payload' => json_encode($normalizedValue),
            'group' => $group,
            'created_at' => now()->format('Y-m-d H:i:s'),
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ];

        // Thêm tenant_id và project_id nếu có
        if ($tenantId) {
            $data['tenant_id'] = $tenantId;
        }

        if ($project) {
            $data['project_id'] = $project->id;
        }

        // Sử dụng INSERT IGNORE để bỏ qua lỗi duplicate key
        $columns = implode(', ', array_map(fn ($col) => "`{$col}`", array_keys($data)));
        $placeholders = implode(', ', array_fill(0, \count($data), '?'));

        \DB::connection('project')->statement(
            "INSERT IGNORE INTO settings ({$columns}) VALUES ({$placeholders})",
            array_values($data)
        );

        // Trả về record (có thể là record mới hoặc record đã tồn tại)
        return static::where('key', $key)->first();
    }

    public static function getValue($key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        $value = $setting->payload;

        if (\is_array($value) && isset($value['value'])) {
            return $value['value'];
        }

        return $value ?? $default;
    }

    /**
     * Xóa tất cả duplicate keys và tạo lại clean
     */
    public static function cleanDuplicateKeys()
    {
        return \DB::connection('project')->transaction(function () {
            // Tìm tất cả keys bị duplicate
            $duplicateKeys = \DB::connection('project')
                ->table('settings')
                ->select('key')
                ->groupBy('key')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('key');

            $cleaned = 0;
            foreach ($duplicateKeys as $key) {
                // Lấy record đầu tiên để giữ lại data
                $firstRecord = static::where('key', $key)->first();
                if ($firstRecord) {
                    $data = [
                        'key' => $key,
                        'payload' => $firstRecord->payload,
                        'group' => $firstRecord->group,
                        'tenant_id' => $firstRecord->tenant_id,
                        'project_id' => $firstRecord->project_id,
                    ];

                    // Xóa tất cả records với key này
                    static::where('key', $key)->delete();

                    // Tạo lại record mới
                    static::create($data);
                    $cleaned++;
                }
            }

            \Log::info("Cleaned {$cleaned} duplicate keys");

            return $cleaned;
        });
    }
}
