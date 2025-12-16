<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('settings')->insertOrIgnore([
            ['key' => 'site_name', 'value' => 'My Agency CMS', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_email', 'value' => 'info@example.com', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
