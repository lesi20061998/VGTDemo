<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insertOrIgnore([
            ['name' => 'Web Development', 'slug' => 'web-development', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'SEO', 'slug' => 'seo', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Marketing', 'slug' => 'marketing', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
