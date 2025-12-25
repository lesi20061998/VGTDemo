<?php

namespace App\Widgets\Marketing;

use App\Widgets\BaseWidget;

class ContactFormWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Contact Us');
        $description = $this->get('description', 'Get in touch with us');
        $showPhone = $this->get('show_phone', true);
        $formStyle = $this->get('form_style', 'modern');
        $buttonColor = $this->get('button_color', '#3B82F6');
        $maxMessageLength = $this->get('max_message_length', 500);
        $socialLinks = $this->get('social_links', []);
        
        $styleClasses = $this->getStyleClasses($formStyle);
        
        $html = "<section class=\"contact-form-widget py-12\">";
        $html .= "<div class=\"container mx-auto px-4 max-w-2xl\">";
        
        // Header
        $html .= "<div class=\"text-center mb-8\">";
        $html .= "<h2 class=\"text-3xl font-bold mb-4\">{$title}</h2>";
        if ($description) {
            $html .= "<p class=\"text-gray-600\">{$description}</p>";
        }
        $html .= "</div>";
        
        // Form
        $html .= "<form class=\"{$styleClasses['form']}\" action=\"/contact\" method=\"POST\">";
        $html .= csrf_field();
        
        // Name field
        $html .= "<div class=\"mb-6\">";
        $html .= "<label class=\"{$styleClasses['label']}\">Full Name *</label>";
        $html .= "<input type=\"text\" name=\"name\" required class=\"{$styleClasses['input']}\" placeholder=\"Your full name\">";
        $html .= "</div>";
        
        // Email field
        $html .= "<div class=\"mb-6\">";
        $html .= "<label class=\"{$styleClasses['label']}\">Email Address *</label>";
        $html .= "<input type=\"email\" name=\"email\" required class=\"{$styleClasses['input']}\" placeholder=\"your@email.com\">";
        $html .= "</div>";
        
        // Phone field (conditional)
        if ($showPhone) {
            $html .= "<div class=\"mb-6\">";
            $html .= "<label class=\"{$styleClasses['label']}\">Phone Number</label>";
            $html .= "<input type=\"tel\" name=\"phone\" class=\"{$styleClasses['input']}\" placeholder=\"Your phone number\">";
            $html .= "</div>";
        }
        
        // Message field
        $html .= "<div class=\"mb-6\">";
        $html .= "<label class=\"{$styleClasses['label']}\">Message *</label>";
        $html .= "<textarea name=\"message\" required rows=\"5\" maxlength=\"{$maxMessageLength}\" class=\"{$styleClasses['textarea']}\" placeholder=\"Your message...\"></textarea>";
        $html .= "<div class=\"text-sm text-gray-500 mt-1\">Maximum {$maxMessageLength} characters</div>";
        $html .= "</div>";
        
        // Submit button
        $html .= "<button type=\"submit\" class=\"{$styleClasses['button']}\" style=\"background-color: {$buttonColor}\">";
        $html .= "Send Message";
        $html .= "</button>";
        
        $html .= "</form>";
        
        // Social links
        if (!empty($socialLinks)) {
            $html .= "<div class=\"mt-8 text-center\">";
            $html .= "<p class=\"text-gray-600 mb-4\">Or connect with us on social media:</p>";
            $html .= "<div class=\"flex justify-center space-x-4\">";
            
            foreach ($socialLinks as $link) {
                if (!empty($link['url'])) {
                    $platform = $link['platform'] ?? '';
                    $showIcon = $link['show_icon'] ?? true;
                    
                    $html .= "<a href=\"{$link['url']}\" target=\"_blank\" class=\"text-gray-600 hover:text-gray-800 transition\">";
                    if ($showIcon) {
                        $html .= $this->getSocialIcon($platform);
                    } else {
                        $html .= ucfirst($platform);
                    }
                    $html .= "</a>";
                }
            }
            
            $html .= "</div>";
            $html .= "</div>";
        }
        
        $html .= "</div>";
        $html .= "</section>";
        
        return $html;
    }

    protected function getStyleClasses(string $style): array
    {
        $styles = [
            'classic' => [
                'form' => 'bg-white p-8 rounded-lg shadow-lg border',
                'label' => 'block text-sm font-medium text-gray-700 mb-2',
                'input' => 'w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500',
                'textarea' => 'w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-vertical',
                'button' => 'w-full py-3 px-4 text-white font-medium rounded-md hover:opacity-90 transition'
            ],
            'modern' => [
                'form' => 'bg-white p-8 rounded-2xl shadow-xl',
                'label' => 'block text-sm font-semibold text-gray-800 mb-2',
                'input' => 'w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 transition',
                'textarea' => 'w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 transition resize-vertical',
                'button' => 'w-full py-4 px-6 text-white font-semibold rounded-xl hover:opacity-90 transition transform hover:scale-105'
            ],
            'minimal' => [
                'form' => 'bg-transparent',
                'label' => 'block text-sm font-medium text-gray-600 mb-1',
                'input' => 'w-full px-0 py-3 border-0 border-b-2 border-gray-200 focus:outline-none focus:border-blue-500 bg-transparent',
                'textarea' => 'w-full px-0 py-3 border-0 border-b-2 border-gray-200 focus:outline-none focus:border-blue-500 bg-transparent resize-vertical',
                'button' => 'w-full py-3 px-6 text-white font-medium rounded-none hover:opacity-90 transition'
            ],
            'bordered' => [
                'form' => 'bg-white p-8 border-2 border-gray-300 rounded-lg',
                'label' => 'block text-sm font-medium text-gray-700 mb-2',
                'input' => 'w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500',
                'textarea' => 'w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 resize-vertical',
                'button' => 'w-full py-3 px-6 text-white font-medium border-2 border-transparent rounded-lg hover:opacity-90 transition'
            ]
        ];
        
        return $styles[$style] ?? $styles['modern'];
    }

    protected function getSocialIcon(string $platform): string
    {
        $icons = [
            'facebook' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
            'twitter' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>',
            'instagram' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.297-3.323c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297c.807.875 1.297 2.026 1.297 3.323s-.49 2.448-1.297 3.323c-.875.807-2.026 1.297-3.323 1.297z"/></svg>',
            'linkedin' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
            'youtube' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>'
        ];
        
        return $icons[$platform] ?? '<span>' . ucfirst($platform) . '</span>';
    }

    public function css(): string
    {
        return '<style>
        .contact-form-widget .form-control:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .contact-form-widget .gallery-field img {
            transition: transform 0.2s ease;
        }
        .contact-form-widget .gallery-field img:hover {
            transform: scale(1.05);
        }
        </style>';
    }

    /**
     * Legacy method for backward compatibility
     */
    public static function getConfig(): array
    {
        return [
            'name' => 'Contact Form',
            'description' => 'Contact form with customizable fields',
            'category' => 'marketing',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Form Title', 'type' => 'text', 'default' => 'Contact Us'],
                ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'default' => 'Get in touch with us'],
                ['name' => 'show_phone', 'label' => 'Show Phone Field', 'type' => 'checkbox', 'default' => true],
            ]
        ];
    }
}