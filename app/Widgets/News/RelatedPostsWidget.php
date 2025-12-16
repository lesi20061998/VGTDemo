<?php

namespace App\Widgets\News;

use App\Widgets\BaseWidget;

class RelatedPostsWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Bài viết liên quan');
        
        $relatedPosts = [
            ['title' => 'Hướng dẫn tối ưu SEO cho website', 'slug' => 'huong-dan-toi-uu-seo-website', 'image' => 'https://images.unsplash.com/photo-1432888622747-4eb9a8efeb07?w=300'],
            ['title' => 'Chiến lược content marketing hiệu quả', 'slug' => 'chien-luoc-content-marketing-hieu-qua', 'image' => 'https://images.unsplash.com/photo-1553830591-fddf9c784d53?w=300'],
            ['title' => 'Xu hướng thiết kế web 2024', 'slug' => 'xu-huong-thiet-ke-web-2024', 'image' => 'https://images.unsplash.com/photo-1467232004584-a241de8bcf5d?w=300'],
        ];
        
        $projectCode = request()->route('projectCode');
        $html = "<section class=\"related-posts-widget py-12 bg-gray-50\">";
        $html .= "<div class=\"container mx-auto px-4\">";
        $html .= "<h3 class=\"text-2xl font-bold mb-8\">{$title}</h3>";
        $html .= "<div class=\"grid md:grid-cols-3 gap-6\">";
        
        foreach ($relatedPosts as $post) {
            $blogUrl = $projectCode ? "/{$projectCode}/blog/{$post['slug']}" : "/blog/{$post['slug']}";
            $html .= "<article class=\"related-post bg-white rounded-lg shadow hover:shadow-md transition overflow-hidden\">";
            $html .= "<img src=\"{$post['image']}\" alt=\"{$post['title']}\" class=\"w-full h-32 object-cover\">";
            $html .= "<div class=\"p-4\">";
            $html .= "<h4 class=\"font-semibold text-sm mb-2\">{$post['title']}</h4>";
            $html .= "<a href=\"{$blogUrl}\" class=\"text-blue-600 hover:text-blue-800 text-xs\">Xem thêm →</a>";
            $html .= "</div></article>";
        }
        
        $html .= "</div></div></section>";
        return $html;
    }

    public function css(): string
    {
        return '<style>
        .related-post { transition: all 0.3s ease; }
        .related-post:hover { transform: translateY(-3px); }
        </style>';
    }

    public function js(): string
    {
        return '<script>
        document.querySelectorAll(".related-post").forEach((post, i) => {
            post.style.opacity = "0";
            post.style.transform = "translateX(20px)";
            setTimeout(() => {
                post.style.transition = "all 0.4s ease";
                post.style.opacity = "1";
                post.style.transform = "translateX(0)";
            }, i * 100);
        });
        </script>';
    }

    public static function getConfig(): array
    {
        return [
            'name' => 'Related Posts',
            'description' => 'Show related posts',
            'category' => 'news',
            'icon' => '<path d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Bài viết liên quan'],
            ]
        ];
    }
}
