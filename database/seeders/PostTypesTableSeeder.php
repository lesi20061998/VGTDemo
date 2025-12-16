<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostTypesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('post_types')->insertOrIgnore([
            ['name' => 'Product', 'slug' => 'product', 'has_category' => true, 'has_meta' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Service', 'slug' => 'service', 'has_category' => true, 'has_meta' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Blog', 'slug' => 'blog', 'has_category' => true, 'has_meta' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
