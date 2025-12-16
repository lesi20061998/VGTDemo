<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        $tenantId = session('current_tenant_id');
        
        // Posts
        $posts = [
            ['title' => 'Getting Started with Modern Web Development', 'slug' => 'getting-started-web-dev', 'content' => '<p>Learn the fundamentals of modern web development with our comprehensive guide covering HTML, CSS, JavaScript, and popular frameworks.</p>'],
            ['title' => '10 Tips for Better Code Quality', 'slug' => '10-tips-code-quality', 'content' => '<p>Discover proven strategies to write cleaner, more maintainable code that your team will love working with.</p>'],
            ['title' => 'The Future of AI in Business', 'slug' => 'future-ai-business', 'content' => '<p>Explore how artificial intelligence is transforming industries and creating new opportunities for innovation.</p>'],
            ['title' => 'Building Scalable Applications', 'slug' => 'building-scalable-apps', 'content' => '<p>Best practices and architectural patterns for building applications that can grow with your business.</p>'],
            ['title' => 'Cybersecurity Best Practices', 'slug' => 'cybersecurity-best-practices', 'content' => '<p>Essential security measures every developer should implement to protect user data and prevent breaches.</p>'],
            ['title' => 'Cloud Computing Essentials', 'slug' => 'cloud-computing-essentials', 'content' => '<p>Understanding cloud infrastructure and how to leverage it for maximum efficiency and cost savings.</p>'],
        ];

        foreach ($posts as $post) {
            DB::table('posts')->insertOrIgnore([
                'title' => $post['title'],
                'slug' => $post['slug'],
                'content' => $post['content'],
                'status' => 'published',
                'tenant_id' => $tenantId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
