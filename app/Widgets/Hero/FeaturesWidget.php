<?php

namespace App\Widgets\Hero;

use App\Widgets\BaseWidget;

class FeaturesWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Our Features');
        $features = $this->get('features', [
            ['icon' => 'rocket', 'title' => 'Fast', 'desc' => 'Lightning fast performance'],
            ['icon' => 'shield', 'title' => 'Secure', 'desc' => 'Bank-level security'],
            ['icon' => 'star', 'title' => 'Premium', 'desc' => 'Premium quality']
        ]);
        
        $icons = [
            'rocket' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
            'shield' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
            'star' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>'
        ];
        
        $html = '<section class="py-16 bg-white"><div class="container mx-auto px-4">';
        $html .= "<h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>";
        $html .= '<div class="grid md:grid-cols-3 gap-8">';
        
        foreach ($features as $feature) {
            $iconPath = $icons[$feature['icon']] ?? $icons['star'];
            $html .= "
            <div class=\"feature-card text-center p-6 rounded-lg hover:shadow-lg transition\">
                <div class=\"flex justify-center mb-4\">
                    <svg class=\"w-16 h-16 text-blue-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">{$iconPath}</svg>
                </div>
                <h3 class=\"text-xl font-bold mb-2\">{$feature['title']}</h3>
                <p class=\"text-gray-600\">{$feature['desc']}</p>
            </div>";
        }
        
        $html .= '</div></div></section>';
        return $html;
    }

    public function css(): string
    {
        return '<style>
        .feature-card { transition: all 0.3s ease; }
        .feature-card:hover { transform: translateY(-10px); }
        .feature-icon { animation: bounce 2s infinite; }
        @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        </style>';
    }

    public function js(): string
    {
        return '<script>
        document.querySelectorAll(".feature-card").forEach((card, i) => {
            card.style.animationDelay = (i * 0.2) + "s";
            card.style.animation = "fadeInUp 0.6s ease-out forwards";
        });
        </script>';
    }

    public static function getConfig(): array
    {
        return [
            'name' => 'Features Grid',
            'description' => '3-column features',
            'category' => 'hero',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Our Features'],
                ['name' => 'features', 'label' => 'Features', 'type' => 'repeater', 'default' => [
                    ['icon' => 'rocket', 'title' => 'Fast', 'desc' => 'Lightning fast performance'],
                    ['icon' => 'shield', 'title' => 'Secure', 'desc' => 'Bank-level security'],
                    ['icon' => 'star', 'title' => 'Premium', 'desc' => 'Premium quality']
                ]]
            ]
        ];
    }
}

