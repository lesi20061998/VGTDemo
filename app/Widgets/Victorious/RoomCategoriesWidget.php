<?php

namespace App\Widgets\Victorious;

use App\Widgets\BaseWidget;

class RoomCategoriesWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Victorious Room Categories',
            'description' => 'Room Categories slider',
            'category' => 'victorious',
            'version' => '1.0.0',
            'icon' => 'home',
            'fields' => [
                ['name' => 'title', 'label' => 'Tiêu đề', 'type' => 'text', 'default' => 'ROOM CATEGORIES'],
                ['name' => 'rooms', 'label' => 'Danh sách phòng', 'type' => 'textarea', 'help' => 'Nhập ID sản phẩm, cách nhau bởi dấu phẩy'],
                ['name' => 'show_features', 'label' => 'Hiển thị features', 'type' => 'checkbox', 'default' => true],
                ['name' => 'view_more_text', 'label' => 'Text View More', 'type' => 'text', 'default' => 'VIEW MORE'],
                ['name' => 'book_text', 'label' => 'Text Book', 'type' => 'text', 'default' => 'BOOK ROOM'],
            ],
            'variants' => ['default' => 'Default'],
        ];
    }

    public function render(): string
    {
        $roomsInput = $this->settings['rooms'] ?? '';
        $roomIds = [];
        if (!empty($roomsInput)) {
            if (is_array($roomsInput)) {
                $roomIds = $roomsInput;
            } else {
                $roomIds = array_filter(array_map('trim', explode(',', $roomsInput)));
            }
        }
        
        $rooms = [];
        if (!empty($roomIds)) {
            $rooms = \App\Models\Product::whereIn('id', $roomIds)->get();
        } else {
            // Get some default rooms if none specified
            $rooms = \App\Models\Product::where('status', 'published')
                ->limit(6)
                ->get();
        }

        return view('widgets.victorious.room-categories', [
            'title' => $this->settings['title'] ?? 'ROOM CATEGORIES',
            'rooms' => $rooms,
            'showFeatures' => $this->settings['show_features'] ?? true,
            'viewMoreText' => $this->settings['view_more_text'] ?? 'VIEW MORE',
            'bookText' => $this->settings['book_text'] ?? 'BOOK ROOM',
        ])->render();
    }
}
