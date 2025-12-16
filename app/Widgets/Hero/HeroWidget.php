<?php

namespace App\Widgets\Hero;

use App\Widgets\BaseWidget;

class HeroWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Chào mừng đến với Doanh nghiệp của chúng tôi');
        $subtitle = $this->get('subtitle', 'Giải pháp công nghệ hàng đầu cho doanh nghiệp hiện đại');
        $btnText = $this->get('button_text', 'Khám phá ngay');
        $btnLink = $this->get('button_link', '/products');
        $bgColor = $this->get('bg_color', 'bg-gradient-to-r from-blue-600 to-purple-600');
        
        return "
        <section class=\"hero-section {$bgColor} text-white py-20\">
            <div class=\"container mx-auto px-4 text-center\">
                <h1 class=\"text-5xl font-bold mb-4\">{$title}</h1>
                <p class=\"text-xl mb-8 opacity-90\">{$subtitle}</p>
                <a href=\"{$btnLink}\" class=\"hero-btn inline-block bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition\">{$btnText}</a>
            </div>
        </section>";
    }

    public function css(): string
    {
        return '<style>
        .hero-section { animation: fadeIn 1s ease-in; }
        .hero-section h1 { animation: slideDown 0.8s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideDown { from { transform: translateY(-30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        </style>';
    }

    public function js(): string
    {
        return '<script>
        document.querySelectorAll(".hero-btn").forEach(btn => {
            btn.addEventListener("mouseenter", () => btn.style.transform = "scale(1.05)");
            btn.addEventListener("mouseleave", () => btn.style.transform = "scale(1)");
        });
        </script>';
    }

    public static function getConfig(): array
    {
        return [
            'name' => 'Hero Section',
            'description' => 'Large banner with CTA',
            'category' => 'hero',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Welcome to Our Website'],
                ['name' => 'subtitle', 'label' => 'Subtitle', 'type' => 'text', 'default' => 'Build amazing things with our platform'],
                ['name' => 'button_text', 'label' => 'Button Text', 'type' => 'text', 'default' => 'Get Started'],
                ['name' => 'button_link', 'label' => 'Button Link', 'type' => 'text', 'default' => '#'],
            ]
        ];
    }
}

