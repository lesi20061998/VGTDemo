<?php

namespace App\Widgets\Victorious;

use App\Widgets\BaseWidget;

class ServicesWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Victorious Services',
            'description' => 'Activities and Services với icons',
            'category' => 'victorious',
            'version' => '1.0.0',
            'icon' => 'grid',
            'fields' => [
                ['name' => 'title', 'label' => 'Tiêu đề', 'type' => 'text', 'default' => 'ACTIVITIES AND SERVICES'],
                ['name' => 'services', 'label' => 'Danh sách dịch vụ', 'type' => 'repeatable', 'fields' => [
                    ['name' => 'icon', 'label' => 'Icon', 'type' => 'image'],
                    ['name' => 'name', 'label' => 'Tên dịch vụ', 'type' => 'text'],
                    ['name' => 'link', 'label' => 'Link', 'type' => 'url'],
                ]],
            ],
            'variants' => ['default' => 'Default'],
        ];
    }

    public function render(): string
    {
        return view('widgets.victorious.services', [
            'title' => $this->settings['title'] ?? 'ACTIVITIES AND SERVICES',
            'services' => $this->settings['services'] ?? [],
        ])->render();
    }
}
