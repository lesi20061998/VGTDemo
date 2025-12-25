<?php

namespace App\Widgets\Custom\SimpleText;

use App\Widgets\BaseWidget;

class SimpleTextWidget extends BaseWidget
{
    protected string $name = 'Simple Text Widget';
    protected string $description = 'Widget hiển thị văn bản đơn giản';
    protected string $category = 'content';
    protected string $icon = 'fas fa-text';

    public function render(): string
    {
        $title = $this->getSetting('title', 'Tiêu đề mặc định');
        $content = $this->getSetting('content', 'Nội dung mặc định');
        $text_color = $this->getSetting('text_color', '#000000');
        $background_color = $this->getSetting('background_color', '#ffffff');

        return "
        <div class='simple-text-widget' style='color: {$text_color}; background-color: {$background_color}; padding: 20px; border-radius: 8px;'>
            <h3 style='margin-bottom: 10px;'>{$title}</h3>
            <p style='margin: 0;'>{$content}</p>
        </div>
        ";
    }

    public function getPreview(): string
    {
        return $this->render();
    }

    public function validateSettings(): bool
    {
        $title = $this->getSetting('title');
        $content = $this->getSetting('content');

        if (empty($title) || empty($content)) {
            throw new \Exception('Tiêu đề và nội dung không được để trống');
        }

        return true;
    }

    public static function getMetadataPath(): string
    {
        return __DIR__ . '/widget.json';
    }
}