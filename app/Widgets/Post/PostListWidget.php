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
        $categoryId = $this->get('category_id', null);
        
        // Lấy bài viết từ database
        $query = Post::where('status', 'published')
            ->orderBy('created_at', 'desc');
        
        // Lọc theo danh mục nếu có
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        $posts = $query->limit($limit)->get();
        
        // Fallback nếu không có bài viết
        if ($posts->isEmpty()) {
            return $this->renderEmptyState($title);
        }
        
        $gridClass = $layout === 'grid' ? 'grid md:grid-cols-3 gap-6' : 'space-y-6';
        
        $html = "<section class=\"post-list-widget py-16 bg-gray-50\">
            <div class=\"container mx-auto px-4\">
                <h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>
                <div class=\"{$gridClass}\">";
        
        $projectCode = request()->route('projectCode');
        foreach ($posts as $post) {
            $blogUrl = $projectCode ? "/{$projectCode}/tin-tuc/{$post->slug}" : "/tin-tuc/{$post->slug}";
            $image = $post->featured_image ?: 'https://via.placeholder.com/400x300?text=No+Image';
            $excerpt = $post->excerpt ?: \Str::limit(strip_tags($post->content), 100);
            $date = $post->created_at ? $post->created_at->format('d/m/Y') : '';
            
            if ($layout === 'grid') {
                $html .= "
                <article class=\"post-card bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition\">
                    <img src=\"{$image}\" alt=\"{$post->title}\" class=\"w-full h-48 object-cover\">
                    <div class=\"p-6\">
                        <div class=\"text-gray-400 text-sm mb-2\">{$date}</div>
                        <h3 class=\"text-xl font-bold mb-2 line-clamp-2\">{$post->title}</h3>
                        <p class=\"text-gray-600 mb-4 line-clamp-3\">{$excerpt}</p>
                        <a href=\"{$blogUrl}\" class=\"text-blue-600 hover:text-blue-800 font-semibold\">Đọc thêm →</a>
                    </div>
                </article>";
            } else {
                $html .= "
                <article class=\"post-card bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition flex\">
                    <img src=\"{$image}\" alt=\"{$post->title}\" class=\"w-48 h-32 object-cover\">
                    <div class=\"p-4 flex-1\">
                        <div class=\"text-gray-400 text-sm mb-1\">{$date}</div>
                        <h3 class=\"text-lg font-bold mb-2\">{$post->title}</h3>
                        <p class=\"text-gray-600 text-sm mb-2 line-clamp-2\">{$excerpt}</p>
                        <a href=\"{$blogUrl}\" class=\"text-blue-600 hover:text-blue-800 font-semibold text-sm\">Đọc thêm →</a>
                    </div>
                </article>";
            }
        }
        
        $html .= "</div></div></section>";
        return $html;
    }
    
    protected function renderEmptyState(string $title): string
    {
        return "<section class=\"post-list-widget py-16 bg-gray-50\">
            <div class=\"container mx-auto px-4\">
                <h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>
                <div class=\"text-center text-gray-500 py-12\">
                    <svg class=\"w-16 h-16 mx-auto mb-4 text-gray-300\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z\"></path>
                    </svg>
                    <p>Chưa có bài viết nào</p>
                </div>
            </div>
        </section>";
    }

    public function css(): string
    {
        return '<style>
        .post-card { transition: all 0.3s ease; }
        .post-card:hover { transform: translateY(-5px); }
        .post-card img { transition: transform 0.3s; }
        .post-card:hover img { transform: scale(1.05); }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
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
            'description' => 'Hiển thị danh sách bài viết từ database',
            'category' => 'content',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Tiêu đề', 'type' => 'text', 'default' => 'Tin tức mới nhất'],
                ['name' => 'limit', 'label' => 'Số lượng bài viết', 'type' => 'number', 'default' => 6],
                ['name' => 'layout', 'label' => 'Kiểu hiển thị', 'type' => 'select', 'default' => 'grid', 'options' => ['grid' => 'Lưới', 'list' => 'Danh sách']],
                ['name' => 'category_id', 'label' => 'Danh mục (ID)', 'type' => 'number', 'default' => ''],
            ]
        ];
    }
}
