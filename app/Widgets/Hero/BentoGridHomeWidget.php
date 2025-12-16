<?php

namespace App\Widgets\Hero;

use App\Widgets\BaseWidget;

class BentoGridHomeWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Welcome');
        $items = $this->get('items', []);
        return view('components.widgets.bento-grid-home', compact('title', 'items'))->render();
    }

    public static function getConfig(): array
    {
        return [
            'name' => 'Bento Grid Home',
            'description' => 'Bento grid layout for homepage',
            'icon' => '<path d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Welcome'],
            ]
        ];
    }
}
