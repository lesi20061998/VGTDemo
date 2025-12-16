<?php

return [
    'general' => [
        'label' => 'Cấu hình chung',
        'icon' => 'settings',
        'fields' => [
            'site_name' => ['type' => 'text', 'label' => 'Tên website'],
            'site_logo' => ['type' => 'image', 'label' => 'Logo'],
            'theme_color' => ['type' => 'color', 'label' => 'Màu chủ đề'],
            'bg_type' => ['type' => 'select', 'label' => 'Nền website', 'options' => ['color' => 'Màu', 'gradient' => 'Gradient', 'image' => 'Hình ảnh']],
            'bg_color' => ['type' => 'color', 'label' => 'Màu nền'],
            'bg_gradient_start' => ['type' => 'color', 'label' => 'Gradient - Màu bắt đầu'],
            'bg_gradient_end' => ['type' => 'color', 'label' => 'Gradient - Màu kết thúc'],
            'bg_gradient_direction' => ['type' => 'select', 'label' => 'Gradient - Hướng', 'options' => ['to right' => 'Ngang', 'to bottom' => 'Dọc', 'to bottom right' => 'Chéo']],
            'bg_image' => ['type' => 'image', 'label' => 'Hình nền'],
            'bg_image_size' => ['type' => 'select', 'label' => 'Background Size', 'options' => ['cover' => 'Cover', 'contain' => 'Contain', 'auto' => 'Auto']],
            'bg_image_position' => ['type' => 'select', 'label' => 'Background Position', 'options' => ['center' => 'Center', 'top' => 'Top', 'bottom' => 'Bottom', 'left' => 'Left', 'right' => 'Right']],
            'bg_image_repeat' => ['type' => 'select', 'label' => 'Background Repeat', 'options' => ['no-repeat' => 'No Repeat', 'repeat' => 'Repeat', 'repeat-x' => 'Repeat X', 'repeat-y' => 'Repeat Y']],
        ]
    ],
    'topbar' => [
        'label' => 'Top Bar',
        'icon' => 'align-justify',
        'fields' => [
            'topbar_enabled' => ['type' => 'checkbox', 'label' => 'Hiển thị Top Bar'],
            'topbar_menu_id' => ['type' => 'menu_select', 'label' => 'Menu'],
            'topbar_text' => ['type' => 'text', 'label' => 'Nội dung'],
            'topbar_bg_color' => ['type' => 'color', 'label' => 'Màu nền'],
            'topbar_text_color' => ['type' => 'color', 'label' => 'Màu chữ'],
        ]
    ],
    'header' => [
        'label' => 'Header',
        'icon' => 'layout',
        'fields' => [
            'header_layout' => ['type' => 'select', 'label' => 'Layout', 'options' => ['default' => 'Mặc định', 'centered' => 'Căn giữa', 'minimal' => 'Tối giản']],
            'header_sticky' => ['type' => 'checkbox', 'label' => 'Header dính'],
            'header_bg_color' => ['type' => 'color', 'label' => 'Màu nền'],
            'header_text_color' => ['type' => 'color', 'label' => 'Màu chữ'],
            'show_search' => ['type' => 'checkbox', 'label' => 'Hiển thị tìm kiếm'],
            'show_cart' => ['type' => 'checkbox', 'label' => 'Hiển thị giỏ hàng'],
            'show_account' => ['type' => 'checkbox', 'label' => 'Hiển thị tài khoản'],
        ]
    ],
    'header_mobile' => [
        'label' => 'Header Mobile',
        'icon' => 'smartphone',
        'fields' => [
            'mobile_menu_style' => ['type' => 'select', 'label' => 'Kiểu menu', 'options' => ['sidebar' => 'Sidebar', 'fullscreen' => 'Toàn màn hình']],
            'mobile_show_search' => ['type' => 'checkbox', 'label' => 'Hiển thị tìm kiếm'],
            'mobile_show_cart' => ['type' => 'checkbox', 'label' => 'Hiển thị giỏ hàng'],
        ]
    ],
    'navigation' => [
        'label' => 'Navigation',
        'icon' => 'menu',
        'fields' => [
            'nav_style' => ['type' => 'select', 'label' => 'Kiểu menu', 'options' => ['horizontal' => 'Ngang', 'mega' => 'Mega Menu']],
            'nav_bg_color' => ['type' => 'color', 'label' => 'Màu nền'],
            'nav_text_color' => ['type' => 'color', 'label' => 'Màu chữ'],
            'nav_hover_color' => ['type' => 'color', 'label' => 'Màu hover'],
        ]
    ],
    'map' => [
        'label' => 'Map',
        'icon' => 'map-pin',
        'fields' => [
            'map_enabled' => ['type' => 'checkbox', 'label' => 'Hiển thị bản đồ'],
            'map_iframe' => ['type' => 'textarea', 'label' => 'Google Maps Iframe'],
        ]
    ],
    'footer' => [
        'label' => 'Footer',
        'icon' => 'align-bottom',
        'fields' => [
            'footer_layout' => ['type' => 'select', 'label' => 'Layout', 'options' => ['3-columns' => '3 cột', '4-columns' => '4 cột']],
            'footer_bg_color' => ['type' => 'color', 'label' => 'Màu nền'],
            'footer_text_color' => ['type' => 'color', 'label' => 'Màu chữ'],
            'footer_copyright' => ['type' => 'textarea', 'label' => 'Copyright'],
            'footer_about' => ['type' => 'textarea', 'label' => 'Giới thiệu'],
        ]
    ],
    'branches' => [
        'label' => 'Chi Nhánh',
        'icon' => 'map',
        'fields' => [
            'show_branches' => ['type' => 'checkbox', 'label' => 'Hiển thị chi nhánh'],
            'branches_title' => ['type' => 'text', 'label' => 'Tiêu đề'],
        ]
    ],
    'posts' => [
        'label' => 'Bài viết',
        'icon' => 'file-text',
        'fields' => [
            'posts_per_page' => ['type' => 'number', 'label' => 'Số bài/trang', 'default' => 12],
            'show_author' => ['type' => 'checkbox', 'label' => 'Hiển thị tác giả'],
            'show_date' => ['type' => 'checkbox', 'label' => 'Hiển thị ngày'],
            'show_excerpt' => ['type' => 'checkbox', 'label' => 'Hiển thị mô tả ngắn'],
        ]
    ],
    'products' => [
        'label' => 'Sản phẩm',
        'icon' => 'shopping-bag',
        'fields' => [
            'products_per_page' => ['type' => 'number', 'label' => 'Số sản phẩm/trang', 'default' => 16],
            'show_quick_view' => ['type' => 'checkbox', 'label' => 'Quick view'],
            'show_compare' => ['type' => 'checkbox', 'label' => 'So sánh sản phẩm'],
            'show_wishlist' => ['type' => 'checkbox', 'label' => 'Yêu thích'],
        ]
    ],
    'floating_cart' => [
        'label' => 'Nút giỏ hàng nổi',
        'icon' => 'shopping-cart',
        'fields' => [
            'floating_cart_enabled' => ['type' => 'checkbox', 'label' => 'Hiển thị'],
            'floating_cart_position' => ['type' => 'select', 'label' => 'Vị trí', 'options' => ['bottom-right' => 'Dưới phải', 'bottom-left' => 'Dưới trái', 'top-right' => 'Trên phải']],
            'floating_cart_color' => ['type' => 'color', 'label' => 'Màu nút'],
        ]
    ],
    'contact_form' => [
        'label' => 'Form tư vấn',
        'icon' => 'message-square',
        'fields' => [
            'form_enabled' => ['type' => 'checkbox', 'label' => 'Hiển thị form'],
            'form_title' => ['type' => 'text', 'label' => 'Tiêu đề'],
            'form_position' => ['type' => 'select', 'label' => 'Vị trí', 'options' => ['sidebar' => 'Sidebar', 'popup' => 'Popup', 'footer' => 'Footer']],
            'form_fields' => ['type' => 'textarea', 'label' => 'Các trường (JSON)'],
        ]
    ],
];
