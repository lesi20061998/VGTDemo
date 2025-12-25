<?php

namespace App\Widgets\Custom\TestWidget;

use App\Widgets\BaseWidget;

class TestWidgetWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Default Title');
        $description = $this->get('description', 'Default description for TestWidget widget');
        $isActive = $this->get('is_active', true);
        
        if (!$isActive) {
            return '';
        }
        
        return "
        <section class=\"test_widget-widget py-8\">
            <div class=\"container mx-auto px-4\">
                <h2 class=\"text-2xl font-bold mb-4\">{$title}</h2>
                <p class=\"text-gray-600\">{$description}</p>
            </div>
        </section>";
    }

    public function css(): string
    {
        return '<style>
        .test_widget-widget {
            /* Add your custom CSS here */
        }
        </style>';
    }

    public function js(): string
    {
        return '<script>
        // Add your custom JavaScript here
        console.log("TestWidgetWidget loaded");
        </script>';
    }

    /**
     * Legacy method for backward compatibility
     */
    public static function getConfig(): array
    {
        return [
            'name' => 'TestWidget',
            'description' => 'A customizable TestWidget widget',
            'category' => 'Custom',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Default Title'],
                ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'default' => 'Default description'],
                ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox', 'default' => true],
            ]
        ];
    }
}
