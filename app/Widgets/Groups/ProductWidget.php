<?php

namespace App\Widgets\Groups;

use App\Widgets\BaseWidget;

class ProductWidget extends BaseWidget
{
    /**
     * Get widget metadata
     */
    public static function getConfig(): array
    {
        return [
            'name' => 'Product Group',
            'description' => 'Main slider components for homepage',
            'category' => 'Media',
            'version' => '1.0.0',
            'icon' => 'image',
            'group' => 'product',
            'fields' => [
                [
                    'name' => 'slides',
                    'label' => 'Slides',
                    'type' => 'repeatable',
                    'fields' => [
                        [
                            'name' => 'image',
                            'label' => 'Background Image',
                            'type' => 'image'
                        ],
                        [
                            'name' => 'title',
                            'label' => 'Title',
                            'type' => 'text'
                        ],
                        [
                            'name' => 'subtitle',
                            'label' => 'Subtitle',
                            'type' => 'text'
                        ],
                        [
                            'name' => 'button_text',
                            'label' => 'Button Text',
                            'type' => 'text'
                        ],
                        [
                            'name' => 'button_link',
                            'label' => 'Button Link',
                            'type' => 'text'
                        ],
                    ]
                ]
            ],
            'settings' => [
                'cacheable' => true,
                'cache_duration' => 3600
            ]
        ];
    }

    /**
     * Prepare data for the Blade view
     */
    public function getViewData(): array
    {
        // Get the configured slides or an empty array if not set
        $slides = $this->get('slides', []);
        
        // We could also run queries here if it was a Product Widget
        
        return [
            'slides' => $slides
        ];
    }
}
