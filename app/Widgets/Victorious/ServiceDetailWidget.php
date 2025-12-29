<?php

namespace App\Widgets\Victorious;

use App\Widgets\BaseWidget;

class ServiceDetailWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Victorious Service Detail',
            'description' => 'Chi tiết dịch vụ với image và CTA',
            'category' => 'victorious',
            'version' => '1.0.0',
            'icon' => 'document',
            'fields' => [
                ['name' => 'image', 'label' => 'Hình ảnh', 'type' => 'image', 'required' => false],
                ['name' => 'decor_image', 'label' => 'Hình trang trí', 'type' => 'image'],
                ['name' => 'title', 'label' => 'Tiêu đề', 'type' => 'text', 'default' => 'Ocean Luxe Spa'],
                ['name' => 'content', 'label' => 'Nội dung', 'type' => 'textarea'],
                ['name' => 'button_text', 'label' => 'Text nút', 'type' => 'text', 'default' => 'BOOK ROOM'],
                ['name' => 'button_link', 'label' => 'Link nút', 'type' => 'url'],
                ['name' => 'layout', 'label' => 'Layout', 'type' => 'select', 'options' => [
                    'image-left' => 'Image bên trái',
                    'image-right' => 'Image bên phải',
                ], 'default' => 'image-left'],
            ],
            'variants' => ['default' => 'Default'],
        ];
    }

    public function render(): string
    {
        return view('widgets.victorious.service-detail', [
            'image' => $this->settings['image'] ?? '',
            'decorImage' => $this->settings['decor_image'] ?? '',
            'title' => $this->settings['title'] ?? '',
            'content' => $this->settings['content'] ?? '',
            'buttonText' => $this->settings['button_text'] ?? 'BOOK ROOM',
            'buttonLink' => $this->settings['button_link'] ?? '#',
            'layout' => $this->settings['layout'] ?? 'image-left',
        ])->render();
    }
}
