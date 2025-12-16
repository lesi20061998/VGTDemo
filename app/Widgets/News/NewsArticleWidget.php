<?php

namespace App\Widgets\News;

use App\Widgets\BaseWidget;
use App\Models\Post;

class NewsArticleWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Tin tức mới nhất');
        $limit = $this->get('limit', 6);
        
        $posts = collect([
            (object)['title' => 'Công nghệ AI đang thay đổi thế giới', 'slug' => 'cong-nghe-ai-thay-doi-the-gioi', 'image' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=400', 'content' => 'Trí tuệ nhân tạo đang cách mạng hóa mọi ngành công nghiệp và tạo ra những cơ hội mới cho doanh nghiệp.', 'created_at' => '2024-01-15'],
            (object)['title' => 'Xu hướng thương mại điện tử 2024', 'slug' => 'xu-huong-thuong-mai-dien-tu-2024', 'image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400', 'content' => 'Khám phá những xu hướng mới nhất trong lĩnh vực thương mại điện tử và cách ứng dụng vào doanh nghiệp.', 'created_at' => '2024-01-14'],
            (object)['title' => 'Bảo mật thông tin trong kỷ nguyên số', 'slug' => 'bao-mat-thong-tin-ky-nguyen-so', 'image' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=400', 'content' => 'Tầm quan trọng của bảo mật thông tin và các giải pháp hiệu quả để bảo vệ dữ liệu doanh nghiệp.', 'created_at' => '2024-01-13'],
            (object)['title' => 'Chuyển đổi số - Cơ hội và thách thức', 'slug' => 'chuyen-doi-so-co-hoi-thach-thuc', 'image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=400', 'content' => 'Phân tích sâu về quá trình chuyển đổi số và cách thức thành công trong thời đại công nghệ.', 'created_at' => '2024-01-12'],
            (object)['title' => 'Marketing online hiệu quả cho SME', 'slug' => 'marketing-online-hieu-qua-sme', 'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400', 'content' => 'Chiến lược marketing trực tuyến dành cho các doanh nghiệp vừa và nhỏ để tăng trưởng doanh thu.', 'created_at' => '2024-01-11'],
            (object)['title' => 'Tương lai của công nghệ blockchain', 'slug' => 'tuong-lai-cong-nghe-blockchain', 'image' => 'https://images.unsplash.com/photo-1639762681485-074b7f938ba0?w=400', 'content' => 'Khám phá tiềm năng ứng dụng của công nghệ blockchain trong các lĩnh vực khác nhau.', 'created_at' => '2024-01-10'],
        ])->take($limit);
        
        $projectCode = request()->route('projectCode');
        $html = "<section class=\"news-article-widget py-16 bg-white\">";
        $html .= "<div class=\"container mx-auto px-4\">";
        $html .= "<h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>";
        $html .= "<div class=\"grid md:grid-cols-2 lg:grid-cols-3 gap-6\">";
        
        foreach ($posts as $post) {
            $blogUrl = $projectCode ? "/{$projectCode}/blog/{$post->slug}" : "/blog/{$post->slug}";
            $html .= "<article class=\"news-card bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden\">";
            $html .= "<img src=\"{$post->image}\" alt=\"{$post->title}\" class=\"w-full h-48 object-cover\">";
            $html .= "<div class=\"p-6\">";
            $html .= "<h3 class=\"font-bold text-lg mb-2\">{$post->title}</h3>";
            $html .= "<p class=\"text-gray-600 text-sm mb-4\">" . substr(strip_tags($post->content), 0, 120) . "...</p>";
            $html .= "<div class=\"flex justify-between items-center\">";
            $html .= "<span class=\"text-xs text-gray-500\">{$post->created_at}</span>";
            $html .= "<a href=\"{$blogUrl}\" class=\"text-blue-600 hover:text-blue-800 font-semibold text-sm\">Xem thêm →</a>";
            $html .= "</div></div></article>";
        }
        
        $html .= "</div></div></section>";
        return $html;
    }

    public function css(): string
    {
        return '<style>
        .news-card { transition: all 0.3s ease; }
        .news-card:hover { transform: translateY(-5px); }
        .news-card img { transition: transform 0.3s; }
        .news-card:hover img { transform: scale(1.05); }
        </style>';
    }

    public function js(): string
    {
        return '<script>
        document.querySelectorAll(".news-card").forEach((card, i) => {
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
            'name' => 'News Article',
            'description' => 'News articles list',
            'category' => 'news',
            'icon' => '<path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Tin tức mới nhất'],
                ['name' => 'limit', 'label' => 'Number of Posts', 'type' => 'number', 'default' => 6],
            ]
        ];
    }
}
