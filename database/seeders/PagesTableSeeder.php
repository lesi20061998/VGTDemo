<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pages')->insertOrIgnore([
            [
                'title' => 'Home',
                'slug' => 'home',
                'status' => 'published',
                'content' => '<h1>Welcome to our CMS</h1>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'status' => 'published',
                'content' => '<h1>About Our Company</h1>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
