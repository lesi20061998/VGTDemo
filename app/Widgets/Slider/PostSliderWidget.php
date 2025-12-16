<?php

namespace App\Widgets\Slider;

use App\Widgets\BaseWidget;
use App\Models\Post;

class PostSliderWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Featured Posts');
        $limit = $this->get('limit', 5);
        
        $posts = Post::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        
        if ($posts->isEmpty()) {
            $posts = collect([
                (object)['title' => 'Success Story: How We Increased Revenue by 250%', 'slug' => '#', 'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800', 'content' => 'Discover how our innovative approach helped a leading e-commerce company triple their revenue in just 6 months through strategic optimization and data-driven decisions.'],
                (object)['title' => 'Case Study: Digital Transformation Journey', 'slug' => '#', 'image' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=800', 'content' => 'Learn how a traditional manufacturing company successfully transitioned to digital operations, improving efficiency by 180% and reducing costs significantly.'],
                (object)['title' => 'Innovation Spotlight: AI-Powered Solutions', 'slug' => '#', 'image' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800', 'content' => 'Explore cutting-edge AI implementations that are revolutionizing customer service and automating complex business processes with remarkable accuracy.'],
                (object)['title' => 'Industry Report: 2024 Technology Trends', 'slug' => '#', 'image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800', 'content' => 'Our comprehensive analysis of emerging technologies and their impact on business operations, featuring insights from industry leaders and market research.'],
                (object)['title' => 'Best Practices: Building High-Performance Teams', 'slug' => '#', 'image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800', 'content' => 'Proven strategies for creating and managing remote teams that deliver exceptional results, featuring real-world examples and actionable frameworks.']
            ])->take($limit);
        }
        
        $html = "<section class=\"post-slider-widget py-16\">
            <div class=\"container mx-auto px-4\">
                <h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>
                <div class=\"slider-container relative overflow-hidden\">
                    <div class=\"slider-track flex transition-transform duration-500\">";
        
        $projectCode = request()->route('projectCode');
        foreach ($posts as $post) {
            $blogUrl = $projectCode ? "/{$projectCode}/blog/{$post->slug}" : "/blog/{$post->slug}";
            $html .= "
                <div class=\"slide min-w-full px-4\">
                    <div class=\"bg-white rounded-lg shadow-lg overflow-hidden\">
                        <div class=\"md:flex\">
                            <img src=\"{$post->image}\" alt=\"{$post->title}\" class=\"md:w-1/2 h-64 md:h-auto object-cover\">
                            <div class=\"p-8 md:w-1/2\">
                                <h3 class=\"text-3xl font-bold mb-4\">{$post->title}</h3>
                                <p class=\"text-gray-600 mb-6\">" . substr(strip_tags($post->content), 0, 200) . "...</p>
                                <a href=\"{$blogUrl}\" class=\"inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition\">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>";
        }
        
        $html .= "</div>
                    <button class=\"slider-prev absolute left-4 top-1/2 -translate-y-1/2 bg-white rounded-full p-3 shadow-lg hover:bg-gray-100\">
                        <svg class=\"w-6 h-6\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M15 19l-7-7 7-7\"/></svg>
                    </button>
                    <button class=\"slider-next absolute right-4 top-1/2 -translate-y-1/2 bg-white rounded-full p-3 shadow-lg hover:bg-gray-100\">
                        <svg class=\"w-6 h-6\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 5l7 7-7 7\"/></svg>
                    </button>
                </div>
            </div>
        </section>";
        
        return $html;
    }

    public function css(): string
    {
        return '<style>
        .slider-container { position: relative; }
        .slider-track { display: flex; }
        .slide { flex-shrink: 0; }
        </style>';
    }

    public function js(): string
    {
        return '<script>
        (function() {
            let currentSlide = 0;
            const track = document.querySelector(".slider-track");
            const slides = document.querySelectorAll(".slide");
            const totalSlides = slides.length;
            
            document.querySelector(".slider-next").addEventListener("click", () => {
                currentSlide = (currentSlide + 1) % totalSlides;
                track.style.transform = `translateX(-${currentSlide * 100}%)`;
            });
            
            document.querySelector(".slider-prev").addEventListener("click", () => {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                track.style.transform = `translateX(-${currentSlide * 100}%)`;
            });
            
            setInterval(() => {
                currentSlide = (currentSlide + 1) % totalSlides;
                track.style.transform = `translateX(-${currentSlide * 100}%)`;
            }, 5000);
        })();
        </script>';
    }

    public static function getConfig(): array
    {
        return [
            'name' => 'Post Slider',
            'description' => 'Featured posts carousel',
            'category' => 'content',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Câu chuyện thành công'],
                ['name' => 'limit', 'label' => 'Number of Posts', 'type' => 'number', 'default' => 5],
            ]
        ];
    }
}

