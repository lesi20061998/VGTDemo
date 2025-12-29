<?php

namespace App\Widgets\Victorious;

use App\Widgets\BaseWidget;

class SpecialOffersWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Victorious Special Offers',
            'description' => 'Special Offers grid',
            'category' => 'victorious',
            'version' => '1.0.0',
            'icon' => 'tag',
            'fields' => [
                ['name' => 'title', 'label' => 'Tiêu đề', 'type' => 'text', 'default' => 'SPECIAL OFFERS'],
                ['name' => 'view_all_link', 'label' => 'Link View All', 'type' => 'url'],
                ['name' => 'offers_large', 'label' => 'Offers lớn (2 items)', 'type' => 'repeatable', 'max_items' => 2, 'fields' => [
                    ['name' => 'image', 'label' => 'Hình ảnh', 'type' => 'image'],
                    ['name' => 'title', 'label' => 'Tiêu đề', 'type' => 'text'],
                    ['name' => 'link', 'label' => 'Link', 'type' => 'url'],
                ]],
                ['name' => 'offers_small', 'label' => 'Offers nhỏ (3 items)', 'type' => 'repeatable', 'max_items' => 3, 'fields' => [
                    ['name' => 'image', 'label' => 'Hình ảnh', 'type' => 'image'],
                    ['name' => 'title', 'label' => 'Tiêu đề', 'type' => 'text'],
                    ['name' => 'link', 'label' => 'Link', 'type' => 'url'],
                ]],
            ],
            'variants' => ['default' => 'Default'],
        ];
    }

    public function render(): string
    {
        return view('widgets.victorious.special-offers', [
            'title' => $this->settings['title'] ?? 'SPECIAL OFFERS',
            'viewAllLink' => $this->settings['view_all_link'] ?? '#',
            'offersLarge' => $this->settings['offers_large'] ?? [],
            'offersSmall' => $this->settings['offers_small'] ?? [],
        ])->render();
    }
}
