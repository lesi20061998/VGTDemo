<?php

namespace App\Widgets\Marketing;

use App\Widgets\BaseWidget;

class CtaWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Sẵn sàng bắt đầu?');
        $subtitle = $this->get('subtitle', 'Hơn 1000+ khách hàng đã tin tưởng sử dụng dịch vụ');
        $btnText = $this->get('button_text', 'Liên hệ ngay');
        $btnLink = $this->get('button_link', '/contact');
        
        return "
        <section class=\"cta-section py-16 text-white\">
            <div class=\"container mx-auto px-4 text-center\">
                <h2 class=\"text-4xl font-bold mb-4\">{$title}</h2>
                <p class=\"text-xl mb-8 opacity-80\">{$subtitle}</p>
                <a href=\"{$btnLink}\" class=\"cta-btn inline-block bg-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition\">{$btnText}</a>
            </div>
        </section>";
    }

    public function css(): string
    {
        return '<style>
        .cta-section { background: linear-gradient(135deg, #1f2937 0%, #111827 100%); }
        .cta-btn { box-shadow: 0 10px 25px rgba(59, 130, 246, 0.5); transition: all 0.3s; }
        .cta-btn:hover { box-shadow: 0 15px 35px rgba(59, 130, 246, 0.7); transform: translateY(-3px); }
        </style>';
    }

    public function js(): string
    {
        return '<script>
        document.querySelectorAll(".cta-btn").forEach(btn => {
            btn.addEventListener("click", function(e) {
                this.style.transform = "scale(0.95)";
                setTimeout(() => this.style.transform = "translateY(-3px)", 150);
            });
        });
        </script>';
    }

    public static function getConfig(): array
    {
        return [
            'name' => 'Call to Action',
            'description' => 'CTA with button',
            'category' => 'marketing',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Ready to get started?'],
                ['name' => 'subtitle', 'label' => 'Subtitle', 'type' => 'text', 'default' => 'Join thousands of satisfied customers'],
                ['name' => 'button_text', 'label' => 'Button Text', 'type' => 'text', 'default' => 'Sign Up Now'],
                ['name' => 'button_link', 'label' => 'Button Link', 'type' => 'text', 'default' => '#'],
            ]
        ];
    }
}

