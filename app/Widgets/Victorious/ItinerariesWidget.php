<?php

namespace App\Widgets\Victorious;

use App\Widgets\BaseWidget;

class ItinerariesWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Victorious Itineraries',
            'description' => 'Our Itineraries grid',
            'category' => 'victorious',
            'version' => '1.0.0',
            'icon' => 'calendar',
            'fields' => [
                ['name' => 'title', 'label' => 'Tiêu đề', 'type' => 'text', 'default' => 'OUR ITINERARIES'],
                ['name' => 'itineraries', 'label' => 'Danh sách lịch trình', 'type' => 'repeatable', 'fields' => [
                    ['name' => 'image', 'label' => 'Hình ảnh', 'type' => 'image'],
                    ['name' => 'duration', 'label' => 'Thời gian', 'type' => 'text'],
                    ['name' => 'link', 'label' => 'Link', 'type' => 'url'],
                ]],
                ['name' => 'button_text', 'label' => 'Text nút', 'type' => 'text', 'default' => 'VIEW MORE'],
            ],
            'variants' => ['default' => 'Default'],
        ];
    }

    public function render(): string
    {
        return view('widgets.victorious.itineraries', [
            'title' => $this->settings['title'] ?? 'OUR ITINERARIES',
            'itineraries' => $this->settings['itineraries'] ?? [],
            'buttonText' => $this->settings['button_text'] ?? 'VIEW MORE',
        ])->render();
    }
}
