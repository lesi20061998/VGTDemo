<?php

namespace App\Widgets\Marketing;

use App\Widgets\BaseWidget;

class TestimonialWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Khách hàng nói gì về chúng tôi');
        $testimonials = $this->get('testimonials', [
            ['name' => 'Nguyễn Văn A', 'role' => 'Giám đốc Công ty ABC', 'content' => 'Dịch vụ tuyệt vời, đội ngũ chuyên nghiệp. Chúng tôi rất hài lòng!', 'avatar' => 'https://ui-avatars.com/api/?name=Nguyen+Van+A&background=4F46E5&color=fff'],
            ['name' => 'Trần Thị B', 'role' => 'Trưởng phòng Marketing', 'content' => 'Giải pháp hiệu quả, tiết kiệm thời gian và chi phí đáng kể.', 'avatar' => 'https://ui-avatars.com/api/?name=Tran+Thi+B&background=EC4899&color=fff'],
            ['name' => 'Lê Minh C', 'role' => 'CTO Startup XYZ', 'content' => 'Công nghệ tiên tiến, hỗ trợ tận tình. Đáng đầu tư!', 'avatar' => 'https://ui-avatars.com/api/?name=Le+Minh+C&background=10B981&color=fff'],
        ]);
        
        $html = "<section class=\"testimonial-widget py-16 bg-white\">
            <div class=\"container mx-auto px-4\">
                <h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>
                <div class=\"grid md:grid-cols-2 gap-8\">";
        
        foreach ($testimonials as $testimonial) {
            $html .= "
                <div class=\"testimonial-card bg-gray-50 rounded-lg p-8 shadow-sm hover:shadow-lg transition\">
                    <div class=\"flex items-center mb-4\">
                        <img src=\"{$testimonial['avatar']}\" alt=\"{$testimonial['name']}\" class=\"w-16 h-16 rounded-full mr-4\">
                        <div>
                            <h4 class=\"font-bold text-lg\">{$testimonial['name']}</h4>
                            <p class=\"text-gray-600 text-sm\">{$testimonial['role']}</p>
                        </div>
                    </div>
                    <p class=\"text-gray-700 italic\">\"{$testimonial['content']}\"</p>
                    <div class=\"flex mt-4 text-yellow-500\">
                        <svg class=\"w-5 h-5\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path d=\"M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z\"/></svg>
                        <svg class=\"w-5 h-5\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path d=\"M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z\"/></svg>
                        <svg class=\"w-5 h-5\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path d=\"M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z\"/></svg>
                        <svg class=\"w-5 h-5\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path d=\"M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z\"/></svg>
                        <svg class=\"w-5 h-5\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path d=\"M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z\"/></svg>
                    </div>
                </div>";
        }
        
        $html .= "</div></div></section>";
        return $html;
    }

    public function css(): string
    {
        return '<style>
        .testimonial-card { position: relative; }
        .testimonial-card::before { content: """; position: absolute; top: 20px; left: 20px; font-size: 80px; color: rgba(0,0,0,0.05); font-family: Georgia, serif; }
        .testimonial-card:hover { transform: translateY(-5px); }
        </style>';
    }

    public function js(): string
    {
        return '<script>
        document.querySelectorAll(".testimonial-card").forEach((card, i) => {
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
            'name' => 'Testimonials',
            'description' => 'Customer reviews',
            'category' => 'marketing',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Khách hàng nói gì về chúng tôi'],
                ['name' => 'testimonials', 'label' => 'Testimonials', 'type' => 'repeater', 'default' => []],
            ]
        ];
    }
}

