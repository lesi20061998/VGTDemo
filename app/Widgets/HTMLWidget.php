<?php

namespace App\Widgets;

class HTMLWidget extends BaseWidget
{
    public function render(): string
    {
        $content = $this->getConfig('content', '');
        $styles = $this->buildStyles();
        
        return "<div class=\"html-widget\" {$styles}>{$content}</div>";
    }

    public function getData(): array
    {
        return [
            'content' => $this->getConfig('content', ''),
        ];
    }
}

