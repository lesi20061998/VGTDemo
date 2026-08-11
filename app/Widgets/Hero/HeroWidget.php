<?php

namespace App\Widgets\Hero;

use App\Widgets\BaseWidget;

class HeroWidget extends BaseWidget
{
    public function render(): string
    {
        // Basic fields
        $title = $this->get('title', 'Chào mừng đến với Doanh nghiệp của chúng tôi');
        $subtitle = $this->get('subtitle', 'Giải pháp công nghệ hàng đầu cho doanh nghiệp hiện đại');
        $btnText = $this->get('button_text', 'Khám phá ngay');
        $btnLink = $this->get('button_link', '#');

        // Custom CSS/JS and code injections
        $customCss = $this->get('custom_css', '');
        $customJs = $this->get('custom_js', '');
        $bodyCode = $this->get('body_code', '');
        $footerCode = $this->get('footer_code', '');

        // Background handling (color/gradient/image)
        $bgInfo = $this->getWrapperBackgroundInfo();
        $extraClasses = $bgInfo['classes'];
        $classString = trim('hero-section text-white py-20 '.implode(' ', $extraClasses));

        // Merge all inline styles into one: background + margin/padding
        $allStyleParts = [];
        if (! empty($bgInfo['style'])) {
            $allStyleParts[] = $bgInfo['style'];
        }
        foreach (['margin_top', 'margin_bottom', 'margin_left', 'margin_right', 'padding_top', 'padding_bottom', 'padding_left', 'padding_right'] as $prop) {
            $val = $this->get($prop);
            if ($val) {
                $cssProp = str_replace('_', '-', $prop);
                $allStyleParts[] = "{$cssProp}: {$val}";
            }
        }
        $styleAttr = ! empty($allStyleParts) ? ' style="'.implode('; ', $allStyleParts).';"' : '';

        // Typography for title
        $titleClass = trim('text-5xl mb-4 '.$this->get('title_font_weight', 'font-bold'));
        $titleStyle = $this->get('title_color') ? ' style="color: '.$this->get('title_color').';"' : '';
        // Typography for subtitle
        $subtitleClass = trim('text-xl mb-8 opacity-90 '.$this->get('description_font_weight', ''));
        $subtitleStyle = $this->get('description_color') ? ' style="color: '.$this->get('description_color').';"' : '';

        // Assemble HTML
        $html = $customCss ? '<style>'.$customCss.'</style>' : '';
        if ($bodyCode) {
            $html .= $bodyCode."\n";
        }
        $html .= '<section class="'.$classString.'"'.$styleAttr.'>';
        $html .= '<div class="container mx-auto px-4 text-center">';
        if (! empty($title)) {
            $html .= '<h1 class="'.$titleClass.'"'.$titleStyle.'>'.htmlspecialchars($title).'</h1>';
        }
        if (! empty($subtitle)) {
            $html .= '<p class="'.$subtitleClass.'"'.$subtitleStyle.'>'.htmlspecialchars($subtitle).'</p>';
        }
        if (! empty($btnText)) {
            $html .= '<a href="'.htmlspecialchars($btnLink ?: '#').'" class="hero-btn inline-block bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">'.htmlspecialchars($btnText).'</a>';
        }
        $html .= '</div>';
        $html .= '</section>';
        if ($customJs) {
            $html .= '<script>'.$customJs.'</script>';
        }
        if ($footerCode) {
            $html .= "\n".$footerCode;
        }

        return $html;
    }

    /**
     * Legacy method for backward compatibility
     */
    public static function getConfig(): array
    {
        return [
            'name' => 'Hero Section',
            'description' => 'Large banner with CTA',
            'category' => 'hero',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Welcome to Our Website'],
                ['name' => 'subtitle', 'label' => 'Subtitle', 'type' => 'text', 'default' => 'Build amazing things with our platform'],
                ['name' => 'button_text', 'label' => 'Button Text', 'type' => 'text', 'default' => 'Get Started'],
                ['name' => 'button_link', 'label' => 'Button Link', 'type' => 'url', 'default' => '#'],
                [
                    'name' => 'bg_color',
                    'label' => 'Background Color',
                    'type' => 'select',
                    'default' => 'bg-gradient-to-r from-blue-600 to-purple-600',
                    'options' => [
                        'bg-white' => 'White',
                        'bg-gray-100' => 'Light Gray',
                        'bg-gray-800' => 'Dark Gray',
                        'bg-blue-600' => 'Blue',
                        'bg-purple-600' => 'Purple',
                        'bg-green-600' => 'Green',
                        'bg-red-600' => 'Red',
                        'bg-black' => 'Black',
                        'bg-gradient-to-r from-blue-600 to-purple-600' => 'Blue to Purple',
                        'bg-gradient-to-r from-green-500 to-blue-600' => 'Green to Blue',
                        'bg-gradient-to-r from-purple-600 to-pink-600' => 'Purple to Pink',
                        'bg-gradient-to-r from-orange-500 to-red-600' => 'Orange to Red',
                    ],
                    'help' => 'Choose background color or gradient',
                ],
                [
                    'name' => 'background_image',
                    'label' => 'Background Image',
                    'type' => 'image',
                    'default' => '',
                    'help' => 'Optional background image (will overlay with background color)',
                ],
            ],
        ];
    }
}
