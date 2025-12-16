<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediaTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('media')->insertOrIgnore([
            [
                'file_name' => 'sample-image.jpg',
                'file_path' => '/uploads/sample-image.jpg',
                'file_type' => 'image/jpeg',
                'file_size' => 1024000,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}