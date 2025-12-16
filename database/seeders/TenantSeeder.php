<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::firstOrCreate(
            ['code' => 'SiVGT'],
            [
                'name' => 'Auto Project SiVGT',
                'domain' => 'localhost',
                'database_name' => 'agency_cms',
                'status' => 'active',
                'settings' => [
                    'theme' => 'default',
                    'language' => 'vi',
                    'timezone' => 'Asia/Ho_Chi_Minh'
                ]
            ]
        );

        echo "✅ Đã tạo tenant SiVGT\n";
    }
}