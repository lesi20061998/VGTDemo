<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('posts')->insertOrIgnore([
            [
                'post_type_id' => 1, // Product
                'title' => 'Sample Product 1',
                'slug' => 'sample-product-1',
                'content' => 'This is a demo product content.',
                'status' => 'published',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'post_type_id' => 2, // Service
                'title' => 'Sample Service 1',
                'slug' => 'sample-service-1',
                'content' => 'This is a demo service content.',
                'status' => 'published',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'post_type_id' => 3, // Blog
                'title' => 'Sample Blog Post',
                'slug' => 'sample-blog-post',
                'content' => 'This is a sample blog content.',
                'status' => 'published',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
