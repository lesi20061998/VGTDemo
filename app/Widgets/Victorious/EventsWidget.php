<?php

namespace App\Widgets\Victorious;

use App\Widgets\BaseWidget;

class EventsWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Victorious Events',
            'description' => 'Events list',
            'category' => 'victorious',
            'version' => '1.0.0',
            'icon' => 'calendar-days',
            'fields' => [
                ['name' => 'title', 'label' => 'Tiêu đề', 'type' => 'text', 'default' => 'EVENTS'],
                ['name' => 'posts', 'label' => 'Chọn bài viết', 'type' => 'textarea', 'help' => 'Nhập ID bài viết, cách nhau bởi dấu phẩy'],
                ['name' => 'limit', 'label' => 'Số lượng hiển thị', 'type' => 'number', 'default' => 3],
                ['name' => 'columns', 'label' => 'Số cột', 'type' => 'select', 'options' => [
                    '2' => '2 cột',
                    '3' => '3 cột',
                    '4' => '4 cột',
                ], 'default' => '3'],
            ],
            'variants' => ['default' => 'Default'],
        ];
    }

    public function render(): string
    {
        $postsInput = $this->settings['posts'] ?? '';
        $postIds = [];
        if (!empty($postsInput)) {
            if (is_array($postsInput)) {
                $postIds = $postsInput;
            } else {
                $postIds = array_filter(array_map('trim', explode(',', $postsInput)));
            }
        }
        
        $limit = $this->settings['limit'] ?? 3;
        $posts = [];
        
        if (!empty($postIds)) {
            $posts = \App\Models\Post::whereIn('id', $postIds)->limit($limit)->get();
        } else {
            $posts = \App\Models\Post::where('post_type', 'post')
                ->where('status', 'published')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }

        return view('widgets.victorious.events', [
            'title' => $this->settings['title'] ?? 'EVENTS',
            'posts' => $posts,
            'columns' => $this->settings['columns'] ?? '3',
        ])->render();
    }
}
