<?php

namespace App\Widgets\Examples;

use App\Widgets\BaseWidget;

class FieldTypesDemoWidget extends BaseWidget
{
    public function render(): string
    {
        $variant = $this->getVariant();
        
        switch ($variant) {
            case 'showcase':
                return $this->renderShowcase();
            case 'minimal':
                return $this->renderMinimal();
            default:
                return $this->renderDefault();
        }
    }

    protected function renderDefault(): string
    {
        $textField = $this->get('text_field', 'Default text');
        $textareaField = $this->get('textarea_field', 'Default description');
        $selectField = $this->get('select_field', 'option1');
        $checkboxField = $this->get('checkbox_field', false);
        $emailField = $this->get('email_field', '');
        $urlField = $this->get('url_field', '');
        $numberField = $this->get('number_field', 0);
        $dateField = $this->get('date_field', '');
        $colorField = $this->get('color_field', '#3B82F6');
        $rangeField = $this->get('range_field', 50);
        $imageField = $this->get('image_field', '');
        $galleryField = $this->get('gallery_field', []);
        $repeatableField = $this->get('repeatable_field', []);

        $html = "<section class=\"field-types-demo-widget py-8\" style=\"border-left: 4px solid {$colorField};\">";
        $html .= "<div class=\"container mx-auto px-4\">";
        
        // Header
        $html .= "<div class=\"mb-8\">";
        $html .= "<h2 class=\"text-3xl font-bold mb-2\">{$textField}</h2>";
        $html .= "<p class=\"text-gray-600\">{$textareaField}</p>";
        $html .= "</div>";
        
        // Field demonstrations
        $html .= "<div class=\"grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6\">";
        
        // Basic fields
        $html .= "<div class=\"bg-white p-4 rounded-lg shadow\">";
        $html .= "<h3 class=\"font-semibold mb-3\">Basic Fields</h3>";
        $html .= "<div class=\"space-y-2 text-sm\">";
        $html .= "<div><strong>Select:</strong> {$selectField}</div>";
        $html .= "<div><strong>Checkbox:</strong> " . ($checkboxField ? 'Yes' : 'No') . "</div>";
        $html .= "<div><strong>Number:</strong> {$numberField}</div>";
        $html .= "<div><strong>Range:</strong> {$rangeField}%</div>";
        if ($dateField) {
            $html .= "<div><strong>Date:</strong> {$dateField}</div>";
        }
        $html .= "</div>";
        $html .= "</div>";
        
        // Contact fields
        if ($emailField || $urlField) {
            $html .= "<div class=\"bg-white p-4 rounded-lg shadow\">";
            $html .= "<h3 class=\"font-semibold mb-3\">Contact Info</h3>";
            $html .= "<div class=\"space-y-2 text-sm\">";
            if ($emailField) {
                $html .= "<div><strong>Email:</strong> <a href=\"mailto:{$emailField}\" class=\"text-blue-600\">{$emailField}</a></div>";
            }
            if ($urlField) {
                $html .= "<div><strong>Website:</strong> <a href=\"{$urlField}\" target=\"_blank\" class=\"text-blue-600\">{$urlField}</a></div>";
            }
            $html .= "</div>";
            $html .= "</div>";
        }
        
        // Media fields
        if ($imageField || !empty($galleryField)) {
            $html .= "<div class=\"bg-white p-4 rounded-lg shadow\">";
            $html .= "<h3 class=\"font-semibold mb-3\">Media</h3>";
            
            if ($imageField) {
                $html .= "<div class=\"mb-3\">";
                $html .= "<img src=\"{$imageField}\" alt=\"Demo image\" class=\"w-full h-32 object-cover rounded\">";
                $html .= "</div>";
            }
            
            if (!empty($galleryField)) {
                $html .= "<div class=\"grid grid-cols-2 gap-2\">";
                foreach (array_slice($galleryField, 0, 4) as $image) {
                    $html .= "<img src=\"{$image}\" alt=\"Gallery image\" class=\"w-full h-16 object-cover rounded\">";
                }
                $html .= "</div>";
            }
            
            $html .= "</div>";
        }
        
        $html .= "</div>";
        
        // Repeatable field demonstration
        if (!empty($repeatableField)) {
            $html .= "<div class=\"mt-8\">";
            $html .= "<h3 class=\"text-xl font-semibold mb-4\">Repeatable Items</h3>";
            $html .= "<div class=\"grid grid-cols-1 md:grid-cols-2 gap-4\">";
            
            foreach ($repeatableField as $item) {
                $itemTitle = $item['item_title'] ?? 'Untitled';
                $itemDescription = $item['item_description'] ?? '';
                $itemActive = $item['item_active'] ?? false;
                $itemPriority = $item['item_priority'] ?? 'medium';
                
                $priorityColors = [
                    'low' => 'bg-gray-100 text-gray-800',
                    'medium' => 'bg-yellow-100 text-yellow-800',
                    'high' => 'bg-red-100 text-red-800'
                ];
                
                $priorityClass = $priorityColors[$itemPriority] ?? $priorityColors['medium'];
                
                $html .= "<div class=\"bg-white p-4 rounded-lg shadow " . ($itemActive ? '' : 'opacity-50') . "\">";
                $html .= "<div class=\"flex justify-between items-start mb-2\">";
                $html .= "<h4 class=\"font-medium\">{$itemTitle}</h4>";
                $html .= "<span class=\"px-2 py-1 text-xs rounded {$priorityClass}\">" . ucfirst($itemPriority) . "</span>";
                $html .= "</div>";
                
                if ($itemDescription) {
                    $html .= "<p class=\"text-sm text-gray-600 mb-2\">{$itemDescription}</p>";
                }
                
                $html .= "<div class=\"text-xs text-gray-500\">";
                $html .= "Status: " . ($itemActive ? 'Active' : 'Inactive');
                $html .= "</div>";
                $html .= "</div>";
            }
            
            $html .= "</div>";
            $html .= "</div>";
        }
        
        $html .= "</div>";
        $html .= "</section>";
        
        return $html;
    }

    protected function renderShowcase(): string
    {
        $textField = $this->get('text_field', 'Field Types Showcase');
        $colorField = $this->get('color_field', '#3B82F6');
        
        $html = "<section class=\"field-types-showcase py-12\" style=\"background: linear-gradient(135deg, {$colorField}22 0%, {$colorField}11 100%);\">";
        $html .= "<div class=\"container mx-auto px-4 text-center\">";
        $html .= "<h1 class=\"text-4xl font-bold mb-4\" style=\"color: {$colorField};\">{$textField}</h1>";
        $html .= "<p class=\"text-lg text-gray-600 mb-8\">Demonstrating the power of the Widget Engine field system</p>";
        
        // Show field type icons
        $fieldTypes = [
            'text' => 'Text Input',
            'select' => 'Dropdown',
            'checkbox' => 'Checkbox',
            'image' => 'Image Upload',
            'color' => 'Color Picker',
            'range' => 'Range Slider',
            'repeatable' => 'Repeatable Fields'
        ];
        
        $html .= "<div class=\"grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4\">";
        foreach ($fieldTypes as $type => $label) {
            $html .= "<div class=\"bg-white p-4 rounded-lg shadow-sm\">";
            $html .= "<div class=\"w-8 h-8 mx-auto mb-2 bg-blue-100 rounded-full flex items-center justify-center\">";
            $html .= "<span class=\"text-blue-600 text-xs font-bold\">" . strtoupper(substr($type, 0, 1)) . "</span>";
            $html .= "</div>";
            $html .= "<div class=\"text-xs font-medium\">{$label}</div>";
            $html .= "</div>";
        }
        $html .= "</div>";
        
        $html .= "</div>";
        $html .= "</section>";
        
        return $html;
    }

    protected function renderMinimal(): string
    {
        $textField = $this->get('text_field', 'Demo Widget');
        $checkboxField = $this->get('checkbox_field', false);
        $colorField = $this->get('color_field', '#3B82F6');
        
        return "
        <div class=\"field-demo-minimal p-4 border-l-4\" style=\"border-color: {$colorField};\">
            <h3 class=\"font-semibold\">{$textField}</h3>
            <p class=\"text-sm text-gray-600\">Status: " . ($checkboxField ? 'Active' : 'Inactive') . "</p>
        </div>";
    }

    public function css(): string
    {
        return '<style>
        .field-types-demo-widget {
            font-family: system-ui, -apple-system, sans-serif;
        }
        .field-types-demo-widget .shadow {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        .field-types-showcase {
            position: relative;
        }
        .field-demo-minimal {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }
        </style>';
    }

    public function js(): string
    {
        return '<script>
        document.addEventListener("DOMContentLoaded", function() {
            console.log("Field Types Demo Widget loaded");
            
            // Add hover effects to demo cards
            const cards = document.querySelectorAll(".field-types-demo-widget .shadow");
            cards.forEach(card => {
                card.addEventListener("mouseenter", function() {
                    this.style.transform = "translateY(-2px)";
                    this.style.transition = "transform 0.2s ease";
                });
                
                card.addEventListener("mouseleave", function() {
                    this.style.transform = "translateY(0)";
                });
            });
        });
        </script>';
    }

    /**
     * Legacy method for backward compatibility
     */
    public static function getConfig(): array
    {
        return [
            'name' => 'Field Types Demo',
            'description' => 'Demonstrates all field types',
            'category' => 'examples',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>',
            'fields' => [
                ['name' => 'text_field', 'label' => 'Text Field', 'type' => 'text', 'default' => 'Demo Text'],
                ['name' => 'checkbox_field', 'label' => 'Active', 'type' => 'checkbox', 'default' => true],
            ]
        ];
    }
}