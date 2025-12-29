<?php

namespace App\Widgets\Victorious;

use App\Widgets\BaseWidget;

class AboutWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Victorious About',
            'description' => 'About Us section với image và text',
            'category' => 'victorious',
            'version' => '1.0.0',
            'icon' => 'info-circle',
            'fields' => [
                ['name' => 'section_title', 'label' => 'Tiêu đề Section', 'type' => 'text', 'default' => 'ABOUT US'],
                ['name' => 'image', 'label' => 'Hình ảnh', 'type' => 'image', 'required' => false],
                ['name' => 'decor_image', 'label' => 'Hình trang trí', 'type' => 'image'],
                ['name' => 'title', 'label' => 'Tiêu đề', 'type' => 'text', 'default' => 'CRUISE WHERE YOU FEEL MOST ALIVE'],
                ['name' => 'content', 'label' => 'Nội dung', 'type' => 'textarea'],
                ['name' => 'background_image', 'label' => 'Background Image', 'type' => 'image'],
            ],
            'variants' => ['default' => 'Default'],
        ];
    }

    public function render(): string
    {
        return view('widgets.victorious.about', [
            'sectionTitle' => $this->settings['section_title'] ?? 'ABOUT US',
            'image' => $this->settings['image'] ?? '',
            'decorImage' => $this->settings['decor_image'] ?? '',
            'title' => $this->settings['title'] ?? '',
            'content' => $this->settings['content'] ?? '',
            'backgroundImage' => $this->settings['background_image'] ?? '',
        ])->render();
    }
}
