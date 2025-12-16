<?php

namespace App\Widgets\Marketing;

use App\Widgets\BaseWidget;

class NewsletterWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Đăng ký nhận tin');
        $subtitle = $this->get('subtitle', 'Nhận thông tin mới nhất về sản phẩm và dịch vụ');
        $placeholder = $this->get('placeholder', 'Nhập email của bạn');
        $buttonText = $this->get('button_text', 'Đăng ký');
        
        return "
        <section class=\"newsletter-widget py-16 bg-gradient-to-r from-purple-600 to-blue-600 text-white\">
            <div class=\"container mx-auto px-4 text-center\">
                <h2 class=\"text-4xl font-bold mb-4\">{$title}</h2>
                <p class=\"text-xl mb-8 opacity-90\">{$subtitle}</p>
                <form class=\"newsletter-form max-w-md mx-auto flex gap-2\" onsubmit=\"return handleNewsletterSubmit(event)\">
                    <input type=\"email\" name=\"email\" placeholder=\"{$placeholder}\" required class=\"flex-1 px-4 py-3 rounded-lg text-gray-900\" />
                    <button type=\"submit\" class=\"newsletter-btn bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition\">{$buttonText}</button>
                </form>
                <div class=\"newsletter-message mt-4 hidden\"></div>
            </div>
        </section>";
    }

    public function css(): string
    {
        return '<style>
        .newsletter-widget { position: relative; overflow: hidden; }
        .newsletter-widget::before { content: ""; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%); animation: pulse 4s ease-in-out infinite; }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }
        .newsletter-form input:focus { outline: none; ring: 2px; ring-color: white; }
        </style>';
    }

    public function js(): string
    {
        return '<script>
        function handleNewsletterSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const email = form.email.value;
            const message = form.parentElement.querySelector(".newsletter-message");
            
            fetch("/api/newsletter/subscribe", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify({email})
            })
            .then(res => res.json())
            .then(data => {
                message.classList.remove("hidden");
                message.className = "newsletter-message mt-4 p-3 rounded-lg bg-white text-green-600";
                message.textContent = "Successfully subscribed!";
                form.reset();
            })
            .catch(err => {
                message.classList.remove("hidden");
                message.className = "newsletter-message mt-4 p-3 rounded-lg bg-white text-red-600";
                message.textContent = "Error subscribing. Please try again.";
            });
            
            return false;
        }
        </script>';
    }

    public static function getConfig(): array
    {
        return [
            'name' => 'Newsletter',
            'description' => 'Email subscription form',
            'category' => 'marketing',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Subscribe to Our Newsletter'],
                ['name' => 'subtitle', 'label' => 'Subtitle', 'type' => 'text', 'default' => 'Get the latest updates delivered to your inbox'],
                ['name' => 'placeholder', 'label' => 'Placeholder', 'type' => 'text', 'default' => 'Enter your email'],
                ['name' => 'button_text', 'label' => 'Button Text', 'type' => 'text', 'default' => 'Subscribe'],
            ]
        ];
    }
}

