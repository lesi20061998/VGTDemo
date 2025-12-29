<?php

namespace App\Widgets\Victorious;

use App\Widgets\BaseWidget;

class HeroVideoWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Victorious Hero Video',
            'description' => 'Hero section với video background',
            'category' => 'victorious',
            'version' => '1.0.0',
            'icon' => 'video',
            'fields' => [
                ['name' => 'video_url', 'label' => 'Video', 'type' => 'video', 'required' => false, 'default' => '', 'help' => 'Upload hoặc nhập URL video (MP4, WebM)'],
                ['name' => 'poster_image', 'label' => 'Poster Image', 'type' => 'image', 'help' => 'Hiển thị khi video chưa load'],
                ['name' => 'overlay_opacity', 'label' => 'Overlay Opacity', 'type' => 'number', 'default' => 0, 'min' => 0, 'max' => 100, 'help' => 'Độ mờ overlay (0-100)'],
                ['name' => 'height', 'label' => 'Chiều cao', 'type' => 'select', 'options' => [
                    '100vh' => 'Full Screen',
                    '80vh' => '80%',
                    '60vh' => '60%',
                    '500px' => '500px',
                ], 'default' => '100vh'],
            ],
            'variants' => ['default' => 'Default'],
        ];
    }

    public function render(): string
    {
        $videoUrl = $this->settings['video_url'] ?? '';
        $poster = $this->settings['poster_image'] ?? '';
        $overlay = $this->settings['overlay_opacity'] ?? 0;
        $height = $this->settings['height'] ?? '100vh';

        return view('widgets.victorious.hero-video', [
            'videoUrl' => $videoUrl,
            'poster' => $poster,
            'overlay' => $overlay,
            'height' => $height,
        ])->render();
    }
}
