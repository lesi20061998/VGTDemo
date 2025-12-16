<?php

namespace App\Widgets\News;

use App\Widgets\BaseWidget;
use App\Models\Post;

class NewsFeaturedWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Tin nổi bật');
        $limit = $this->get('limit', 3);
        
        $posts = collect([
            (object)['title' => 'Ra mắt sản phẩm công nghệ đột phá  2024', 'slug' => 'ra-mat-san-pham-cong-nghe-dot-pha-2024', 'image' => 'https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=600', 'content' => 'Sản phẩm mới với công nghệ tiên tiến hứa hẹn sẽ thay đổi cách chúng ta làm việc và sinh hoạt.'],
            (object)['title' => 'Thành công vượt mốc 1 triệu khách hàng', 'slug' => 'thanh-cong-vuot-moc-1-trieu-khach-hang', 'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600', 'content' => 'Cốt mốc quan trọng trong hành trình phát triển, khẳng định vị thế dẫn đầu thị trường.'],
            (object)['title' => 'Giải thưởng "Doanh nghiệp xuất sắc 2024"', 'slug' => 'giai-thuong-doanh-nghiep-xuat-sac-2024', 'image' => 'https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=600', 'content' => 'Vinh dự nhận giải thưởng danh giá từ Hiệp hội Doanh nghiệp Việt Nam vì những đóng góp tích cực.'],
        ])->take($limit);
        
        $projectCode = request()->route('projectCode');
        $html = "<section class=\"news-featured-widget py-16 bg-gradient-to-r from-blue-50 to-purple-50\">";
        $html .= "<div class=\"container mx-auto px-4\">";
        $html .= "<h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>";
        $html .= "<div class=\"grid md:grid-cols-3 gap-8\">";
        
        foreach ($posts as $post) {
            $blogUrl = $projectCode ? "/{$projectCode}/blog/{$post->slug}" : "/blog/{$post->slug}";
            $html .= "<article class=\"featured-card bg-white rounded-xl shadow-lg hover:shadow-xl transition overflow-hidden\">";
            $html .= "<div class=\"relative\">";
            $html .= "<img src=\"{$post->image}\" alt=\"{$post->title}\" class=\"w-full h-56 object-cover\">";
            $html .= "<div class=\"absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold\">NổI BẬT</div>";
            $html .= "</div>";
            $html .= "<div class=\"p-6\">";
            $html .= "<h3 class=\"font-bold text-xl mb-3\">{$post->title}</h3>";
            $html .= "<p class=\"text-gray-600 mb-4\">" . substr(strip_tags($post->content), 0, 100) . "...</p>";
            $html .= "<a href=\"{$blogUrl}\" class=\"inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition\">Chi tiết</a>";
            $html .= "</div></article>";
        }
        
        $html .= "</div></div></section>";
        return $html;
    }

    public function css(): string
    {
        return '<style>
        .featured-card { transition: all 0.3s ease; }
        .featured-card:hover { transform: translateY(-10px); }
        .featured-card img { transition: transform 0.3s; }
        .featured-card:hover img { transform: scale(1.1); }
        </style>';
    }

    public function js(): string
    {
        return '<script>
        document.querySelectorAll(".featured-card").forEach((card, i) => {
            card.style.opacity = "0";
            card.style.transform = "translateY(30px)";
            setTimeout(() => {
                card.style.transition = "all 0.6s ease";
                card.style.opacity = "1";
                card.style.transform = "translateY(0)";
            }, i * 200);
        });
        </script>';
    }

    public static function getConfig(): array
    {
        return [
            'name' => 'News Featured',
            'description' => 'Featured news posts',
            'category' => 'news',
            'icon' => '<path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Tin nổi bật'],
                ['name' => 'limit', 'label' => 'Number of Posts', 'type' => 'number', 'default' => 3],
            ]
        ];
    }
}
