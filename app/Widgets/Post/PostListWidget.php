<?php

namespace App\Widgets\Post;

use App\Widgets\BaseWidget;
use App\Models\Post;

class PostListWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Tin tức & Cập nhật');
        $limit = $this->get('limit', 6);
        $layout = $this->get('layout', 'grid');
        
        $posts = Post::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        
        if ($posts->isEmpty()) {
            $posts = collect([
                (object)['title' => 'Getting Started with Modern Web Development', 'slug' => '#', 'image' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=400', 'content' => 'Learn the fundamentals of modern web development with our comprehensive guide covering HTML, CSS, JavaScript, and popular frameworks.'],
                (object)['title' => '10 Tips for Better Code Quality', 'slug' => '#', 'image' => 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=400', 'content' => 'Discover proven strategies to write cleaner, more maintainable code that your team will love working with.'],
                (object)['title' => 'The Future of AI in Business', 'slug' => '#', 'image' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=400', 'content' => 'Explore how artificial intelligence is transforming industries and creating new opportunities for innovation.'],
                (object)['title' => 'Building Scalable Applications', 'slug' => '#', 'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400', 'content' => 'Best practices and architectural patterns for building applications that can grow with your business.'],
                (object)['title' => 'Cybersecurity Best Practices', 'slug' => '#', 'image' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=400', 'content' => 'Essential security measures every developer should implement to protect user data and prevent breaches.'],
                (object)['title' => 'Cloud Computing Essentials', 'slug' => '#', 'image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=400', 'content' => 'Understanding cloud infrastructure and how to leverage it for maximum efficiency and cost savings.']
            ])->take($limit);
        }
        
        $gridClass = $layout === 'grid' ? 'grid md:grid-cols-3 gap-6' : 'space-y-6';
        
        $html = "<section class=\"post-list-widget py-16 bg-gray-50\">
            <div class=\"container mx-auto px-4\">
                <h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>
                <div class=\"{$gridClass}\">";
        
        $projectCode = request()->route('projectCode');
        foreach ($posts as $post) {
            $blogUrl = $projectCode ? "/{$projectCode}/blog/{$post->slug}" : "/blog/{$post->slug}";
            $html .= "
                <article class=\"post-card bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition\">
                    <img src=\"{$post->image}\" alt=\"{$post->title}\" class=\"w-full h-48 object-cover\">
                    <div class=\"p-6\">
                        <h3 class=\"text-xl font-bold mb-2\">{$post->title}</h3>
                        <p class=\"text-gray-600 mb-4\">" . substr(strip_tags($post->content), 0, 100) . "...</p>
                        <a href=\"{$blogUrl}\" class=\"text-blue-600 hover:text-blue-800 font-semibold\">Read More →</a>
                    </div>
                </article>";
        }
        
        $html .= "</div></div></section>";
        return $html;
    }

    public function css(): string
    {
        return '<style>
        .post-card { transition: all 0.3s ease; }
        .post-card:hover { transform: translateY(-5px); }
        .post-card img { transition: transform 0.3s; }
        .post-card:hover img { transform: scale(1.05); }
        </style>';
    }

    public function js(): string
    {
        return '<script>
        document.querySelectorAll(".post-card").forEach((card, i) => {
            card.style.opacity = "0";
            card.style.transform = "translateY(20px)";
            setTimeout(() => {
                card.style.transition = "all 0.5s ease";
                card.style.opacity = "1";
                card.style.transform = "translateY(0)";
            }, i * 100);
        });
        </script>';
    }

    public static function getConfig(): array
    {
        return [
            'name' => 'Post List',
            'description' => 'Display latest blog posts',
            'category' => 'content',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Latest Posts'],
                ['name' => 'limit', 'label' => 'Number of Posts', 'type' => 'number', 'default' => 6],
                ['name' => 'layout', 'label' => 'Layout', 'type' => 'select', 'default' => 'grid', 'options' => ['grid', 'list']],
            ]
        ];
    }
}

