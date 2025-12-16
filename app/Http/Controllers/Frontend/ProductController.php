<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        $products = collect([
            (object)['name' => 'Laptop Dell XPS 15', 'slug' => 'laptop-dell-xps-15', 'image' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=800', 'description' => 'Laptop cao cấp cho doanh nhân', 'price' => 35000000],
            (object)['name' => 'iPhone 15 Pro Max', 'slug' => 'iphone-15-pro-max', 'image' => 'https://images.unsplash.com/photo-1592286927505-4fd4d3d4ef9f?w=800', 'description' => 'Smartphone flagship mới nhất', 'price' => 32000000],
            (object)['name' => 'MacBook Pro M3', 'slug' => 'macbook-pro-m3', 'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800', 'description' => 'Hiệu năng vượt trội', 'price' => 45000000],
            (object)['name' => 'Samsung Galaxy S24', 'slug' => 'samsung-galaxy-s24', 'image' => 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=800', 'description' => 'Android flagship', 'price' => 25000000],
            (object)['name' => 'iPad Pro 2024', 'slug' => 'ipad-pro-2024', 'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=800', 'description' => 'Máy tính bảng chuyên nghiệp', 'price' => 28000000],
            (object)['name' => 'AirPods Pro 2', 'slug' => 'airpods-pro-2', 'image' => 'https://images.unsplash.com/photo-1606841837239-c5a1a4a07af7?w=800', 'description' => 'Tai nghe chống ồn', 'price' => 6000000],
        ]);
        
        $categories = collect([
            (object)['name' => 'Laptop', 'slug' => 'laptop'],
            (object)['name' => 'Điện thoại', 'slug' => 'dien-thoai'],
            (object)['name' => 'Phụ kiện', 'slug' => 'phu-kien'],
        ]);
        
        return view('frontend.products.index', compact('products', 'categories'));
    }
    
    public function show($slug)
    {
        $product = (object)[
            'name' => 'Laptop Dell XPS 15',
            'slug' => $slug,
            'image' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=800',
            'description' => 'Laptop cao cấp dành cho doanh nhân và chuyên gia sáng tạo với hiệu năng mạnh mẽ',
            'price' => 35000000,
            'specifications' => '<ul class="list-disc pl-5 space-y-2"><li>CPU: Intel Core i7-13700H</li><li>RAM: 16GB DDR5</li><li>SSD: 512GB NVMe</li><li>VGA: NVIDIA RTX 4050</li><li>Màn hình: 15.6" FHD IPS</li></ul>'
        ];
        
        return view('frontend.products.show', compact('product'));
    }
}

