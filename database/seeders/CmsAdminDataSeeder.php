<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Post;
use App\Models\Project;
use App\Models\Taxonomy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CmsAdminDataSeeder extends Seeder
{
    /**
     * Run the database seeds for admin CMS context.
     */
    public function run(): void
    {
        $projectId = session('current_project_id');
        if (! $projectId && app()->bound('current_project_id')) {
            $projectId = app('current_project_id');
        }
        if (! $projectId) {
            $project = Project::first();
            $projectId = $project?->id ?? 1;
        }

        if ($this->command) {
            $this->command->info("Seeding Admin CMS sample data for project ID: {$projectId}");
        }

        // 1. Product Categories (Cấp 1 & Cấp 2)
        $productCategories = [
            [
                'name' => 'Điện thoại & Máy tính bảng',
                'slug' => 'dien-thoai-may-tinh-bang',
                'description' => 'Điện thoại thông minh, máy tính bảng cao cấp',
                'children' => [
                    ['name' => 'iPhone / iOS', 'slug' => 'iphone-ios', 'description' => 'Điện thoại iPhone chính hãng'],
                    ['name' => 'Samsung / Android', 'slug' => 'samsung-android', 'description' => 'Điện thoại Samsung Galaxy'],
                    ['name' => 'iPad & Tablet', 'slug' => 'ipad-tablet', 'description' => 'Máy tính bảng iPad và Android'],
                ],
            ],
            [
                'name' => 'Laptop & Máy tính',
                'slug' => 'laptop-may-tinh',
                'description' => 'Laptop làm việc, đồ họa, gaming chính hãng',
                'children' => [
                    ['name' => 'MacBook', 'slug' => 'macbook', 'description' => 'Laptop Apple MacBook Air / Pro'],
                    ['name' => 'Laptop Gaming', 'slug' => 'laptop-gaming', 'description' => 'Laptop chơi game cấu hình cao'],
                    ['name' => 'Laptop Văn phòng', 'slug' => 'laptop-van-phong', 'description' => 'Laptop mỏng nhẹ, pin trâu'],
                ],
            ],
            [
                'name' => 'Phụ kiện công nghệ',
                'slug' => 'phu-kien-cong-nghe',
                'description' => 'Tai nghe, cáp sạc, pin dự phòng',
                'children' => [
                    ['name' => 'Tai nghe & Loa', 'slug' => 'tai-nghe-loa', 'description' => 'Tai nghe không dây, loa bluetooth'],
                    ['name' => 'Cáp sạc & Pin dự phòng', 'slug' => 'cap-sac-pin-du-phong', 'description' => 'Sạc nhanh, pin dự phòng dung lượng lớn'],
                ],
            ],
        ];

        $createdProductCats = [];

        foreach ($productCategories as $pOrder => $catGroup) {
            $parentCat = Taxonomy::updateOrCreate(
                ['slug' => $catGroup['slug']],
                [
                    'project_id' => $projectId,
                    'tenant_id' => $projectId,
                    'name' => $catGroup['name'],
                    'taxonomy' => 'product_cat',
                    'description' => $catGroup['description'],
                    'parent_id' => null,
                    'order' => $pOrder + 1,
                    'status' => 'published',
                    'meta_data' => [
                        'level' => 0,
                        'path' => $catGroup['slug'],
                        'is_active' => true,
                    ],
                ]
            );
            $createdProductCats[] = $parentCat->id;

            foreach ($catGroup['children'] as $cOrder => $child) {
                $childCat = Taxonomy::updateOrCreate(
                    ['slug' => $child['slug']],
                    [
                        'project_id' => $projectId,
                        'tenant_id' => $projectId,
                        'name' => $child['name'],
                        'taxonomy' => 'product_cat',
                        'description' => $child['description'],
                        'parent_id' => $parentCat->id,
                        'order' => $cOrder + 1,
                        'status' => 'published',
                        'meta_data' => [
                            'level' => 1,
                            'path' => $catGroup['slug'].'/'.$child['slug'],
                            'is_active' => true,
                        ],
                    ]
                );
                $createdProductCats[] = $childCat->id;
            }
        }

        // 2. Post Categories (Tin tức, Đánh giá, Khuyến mãi)
        $postCategories = [
            ['name' => 'Tin tức công nghệ', 'slug' => 'tin-tuc-cong-nghe', 'description' => 'Cập nhật tin tức công nghệ mới nhất'],
            ['name' => 'Đánh giá sản phẩm', 'slug' => 'danh-gia-san-pham', 'description' => 'Review chi tiết các thiết bị hot'],
            ['name' => 'Hướng dẫn & Mẹo hay', 'slug' => 'huong-dan-meo-hay', 'description' => 'Mẹo sử dụng thiết bị hiệu quả'],
            ['name' => 'Chương trình khuyến mãi', 'slug' => 'chuong-trinh-khuyen-mai', 'description' => 'Thông tin ưu đãi và giảm giá'],
        ];

        $createdPostCats = [];
        foreach ($postCategories as $pOrder => $postCat) {
            $cat = Taxonomy::updateOrCreate(
                ['slug' => $postCat['slug']],
                [
                    'project_id' => $projectId,
                    'tenant_id' => $projectId,
                    'name' => $postCat['name'],
                    'taxonomy' => 'category',
                    'description' => $postCat['description'],
                    'parent_id' => null,
                    'order' => $pOrder + 1,
                    'status' => 'published',
                    'meta_data' => ['level' => 0, 'is_active' => true],
                ]
            );
            $createdPostCats[] = $cat->id;
        }

        // 3. Brands
        $brands = [
            ['name' => 'Apple', 'slug' => 'apple', 'description' => 'Thương hiệu công nghệ hàng đầu Mỹ'],
            ['name' => 'Samsung', 'slug' => 'samsung', 'description' => 'Tập đoàn điện tử Hàn Quốc'],
            ['name' => 'Sony', 'slug' => 'sony', 'description' => 'Thương hiệu thiết bị âm thanh & điện tử Nhật Bản'],
            ['name' => 'Asus', 'slug' => 'asus', 'description' => 'Nhà sản xuất máy tính & linh kiện hàng đầu'],
            ['name' => 'JBL', 'slug' => 'jbl', 'description' => 'Thương hiệu âm thanh cao cấp'],
        ];

        foreach ($brands as $bOrder => $brand) {
            Taxonomy::updateOrCreate(
                ['slug' => $brand['slug']],
                [
                    'project_id' => $projectId,
                    'tenant_id' => $projectId,
                    'name' => $brand['name'],
                    'taxonomy' => 'brand',
                    'description' => $brand['description'],
                    'parent_id' => null,
                    'order' => $bOrder + 1,
                    'status' => 'published',
                    'meta_data' => ['is_active' => true],
                ]
            );
        }

        // 4. Products
        $productsData = [
            [
                'title' => 'iPhone 15 Pro Max 256GB Natural Titanium',
                'slug' => 'iphone-15-pro-max-256gb-natural-titanium',
                'sku' => 'IP15PM-256-NAT',
                'price' => 34990000,
                'sale_price' => 32990000,
                'excerpt' => 'iPhone 15 Pro Max khung Titan cao cấp, chip A17 Pro mạnh mẽ, camera zoom 5x ấn tượng.',
                'content' => 'iPhone 15 Pro Max được thiết kế từ chất liệu Titan chuẩn vũ trụ nhẹ và bền bỉ. Màn hình Super Retina XDR 6.7 inch với ProMotion 120Hz mang đến trải nghiệm hiển thị sống động tuyệt vời.',
                'featured_image' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&auto=format&fit=crop&q=60',
                'stock' => 50,
                'cat_index' => 1, // iPhone / iOS
            ],
            [
                'title' => 'Samsung Galaxy S24 Ultra 5G 512GB',
                'slug' => 'samsung-galaxy-s24-ultra-5g-512gb',
                'sku' => 'SS-S24U-512-GRY',
                'price' => 33990000,
                'sale_price' => 29990000,
                'excerpt' => 'Galaxy S24 Ultra tích hợp Galaxy AI thông minh, bút S-Pen tiện lợi, camera 200MP siêu nét.',
                'content' => 'Samsung Galaxy S24 Ultra mở ra kỷ nguyên điện thoại AI mới với các tính năng khoanh vùng tìm kiếm, phiên dịch trực tiếp cuộc gọi và trợ lý chỉnh ảnh thông minh.',
                'featured_image' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=800&auto=format&fit=crop&q=60',
                'stock' => 45,
                'cat_index' => 2, // Samsung / Android
            ],
            [
                'title' => 'Apple MacBook Pro 14 inch M3 16GB / 512GB SSD',
                'slug' => 'apple-macbook-pro-14-inch-m3-16gb-512gb-ssd',
                'sku' => 'MBP14-M3-16512',
                'price' => 39990000,
                'sale_price' => 37490000,
                'excerpt' => 'MacBook Pro 14 M3 sở hữu hiệu năng mạnh mẽ vượt trội, thời lượng pin lên đến 22 giờ liên tục.',
                'content' => 'Với con chip M3 thế hệ mới được sản xuất trên tiến trình 3nm, MacBook Pro 14 inch mang đến khả năng xử lý đồ họa mượt mà và thời lượng pin đột phá.',
                'featured_image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop&q=60',
                'stock' => 30,
                'cat_index' => 4, // MacBook
            ],
            [
                'title' => 'Asus ROG Strix G16 Core i7 / RTX 4060',
                'slug' => 'asus-rog-strix-g16-core-i7-rtx-4060',
                'sku' => 'ROG-G16-2024-I7',
                'price' => 32990000,
                'sale_price' => 29490000,
                'excerpt' => 'Laptop gaming Asus ROG Strix G16 cấu hình đỉnh cao, tản nhiệt Tri-Fan siêu mát.',
                'content' => 'Chiến mượt mọi tựa game AAA mới nhất với card đồ họa NVIDIA GeForce RTX 4060 8GB và màn hình QHD+ 240Hz sắc nét.',
                'featured_image' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=800&auto=format&fit=crop&q=60',
                'stock' => 20,
                'cat_index' => 5, // Laptop Gaming
            ],
            [
                'title' => 'Tai nghe chống ồn không dây Sony WH-1000XM5',
                'slug' => 'tai-nghe-chong-on-khong-day-sony-wh-1000xm5',
                'sku' => 'SONY-XM5-BLK',
                'price' => 8990000,
                'sale_price' => 7490000,
                'excerpt' => 'Sony WH-1000XM5 với công nghệ chống ồn hàng đầu thế giới và chất âm Hi-Res chân thực.',
                'content' => 'Trải nghiệm không gian âm nhạc riêng biệt cùng chiếc tai nghe chụp tai hàng đầu của Sony. Tích hợp 8 micro và bộ xử lý Auto NC Optimizer tối ưu hóa khả năng khử tiếng ồn.',
                'featured_image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=60',
                'stock' => 60,
                'cat_index' => 7, // Tai nghe & Loa
            ],
            [
                'title' => 'Loa Bluetooth di động JBL Charge 5',
                'slug' => 'loa-bluetooth-di-dong-jbl-charge-5',
                'sku' => 'JBL-CHG5-BLK',
                'price' => 3990000,
                'sale_price' => 3490000,
                'excerpt' => 'Loa JBL Charge 5 âm thanh JBL Original Pro mạnh mẽ, chống nước chống bụi chuẩn IP67.',
                'content' => 'Thời gian phát nhạc liên tục lên tới 20 giờ cùng tính năng PartyBoost kết nối nhiều loa cùng lúc giúp khuấy động mọi bữa tiệc ngoài trời.',
                'featured_image' => 'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=800&auto=format&fit=crop&q=60',
                'stock' => 80,
                'cat_index' => 7, // Tai nghe & Loa
            ],
        ];

        $createdProducts = [];

        foreach ($productsData as $pData) {
            $metaData = [
                'sku' => $pData['sku'],
                'price' => $pData['price'],
                'sale_price' => $pData['sale_price'],
                'product_type' => 'simple',
                'stock_quantity' => $pData['stock'],
                'manage_stock' => true,
                'is_featured' => true,
                'gallery' => [
                    $pData['featured_image'],
                ],
            ];

            $product = Post::updateOrCreate(
                ['slug' => $pData['slug']],
                [
                    'project_id' => $projectId,
                    'tenant_id' => $projectId,
                    'title' => $pData['title'],
                    'excerpt' => $pData['excerpt'],
                    'content' => $pData['content'],
                    'featured_image' => $pData['featured_image'],
                    'post_type' => 'product',
                    'status' => 'published',
                    'meta_title' => $pData['title'],
                    'meta_description' => $pData['excerpt'],
                    'meta_data' => $metaData,
                ]
            );

            // Gán category vào term_relationships
            $catId = $createdProductCats[$pData['cat_index'] ?? 0] ?? $createdProductCats[0];
            DB::table('term_relationships')->updateOrInsert(
                ['object_id' => $product->id, 'term_taxonomy_id' => $catId],
                ['order' => 0]
            );

            $createdProducts[] = $product;
        }

        // 5. Blog Posts
        $postsData = [
            [
                'title' => 'Top 5 điện thoại thông minh đáng mua nhất năm 2026',
                'slug' => 'top-5-dien-thoai-thong-minh-dang-mua-nhat-nam-2026',
                'excerpt' => 'Tổng hợp các mẫu smartphone sở hữu hiệu năng mạnh mẽ, camera chụp ảnh đỉnh cao và dung lượng pin ấn tượng.',
                'content' => 'Thị trường smartphone năm 2026 chứng kiến sự bùng nổ của các công nghệ AI tích hợp trực tiếp trên vi xử lý. Các dòng Flagship đến từ Apple, Samsung, Xiaomi mang tới cho người dùng nhiều trải nghiệm vượt trội...',
                'featured_image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&auto=format&fit=crop&q=60',
            ],
            [
                'title' => 'Đánh giá chi tiết MacBook Pro M3 sau 6 tháng sử dụng',
                'slug' => 'danh-gia-chi-tiet-macbook-pro-m3-sau-6-thang-su-dung',
                'excerpt' => 'MacBook Pro M3 có thực sự đáng giá với số tiền bỏ ra? Cùng trải nghiệm thực tế hiệu năng công việc lập trình và thiết kế đồ họa.',
                'content' => 'Sau 6 tháng đồng hành trong công việc hàng ngày, MacBook Pro M3 chứng tỏ đẳng cấp về sự bền bỉ, màn hình Liquid Retina XDR sống động cùng thời lượng pin kéo dài cả ngày làm việc mà không cần cắm sạc...',
                'featured_image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop&q=60',
            ],
            [
                'title' => 'Hướng dẫn tối ưu thời lượng pin cho smartphone Android & iOS',
                'slug' => 'huong-dan-toi-uu-thoi-luong-pin-cho-smartphone-android-ios',
                'excerpt' => 'Những mẹo đơn giản và hiệu quả giúp kéo dài thời gian sử dụng pin điện thoại hàng ngày của bạn.',
                'content' => 'Pin là yếu tố quan trọng ảnh hưởng đến trải nghiệm sử dụng smartphone. Để bảo vệ tuổi thọ pin và tiết kiệm dung lượng, bạn nên tắt các kết nối không cần thiết, giảm độ sáng màn hình và sử dụng chế độ tiết kiệm pin...',
                'featured_image' => 'https://images.unsplash.com/photo-1580910051074-3eb694886505?w=800&auto=format&fit=crop&q=60',
            ],
        ];

        foreach ($postsData as $idx => $postData) {
            $blogPost = Post::updateOrCreate(
                ['slug' => $postData['slug']],
                [
                    'project_id' => $projectId,
                    'tenant_id' => $projectId,
                    'title' => $postData['title'],
                    'excerpt' => $postData['excerpt'],
                    'content' => $postData['content'],
                    'featured_image' => $postData['featured_image'],
                    'post_type' => 'post',
                    'status' => 'published',
                    'meta_title' => $postData['title'],
                    'meta_description' => $postData['excerpt'],
                ]
            );

            $postCatId = $createdPostCats[$idx % count($createdPostCats)] ?? $createdPostCats[0];
            DB::table('term_relationships')->updateOrInsert(
                ['object_id' => $blogPost->id, 'term_taxonomy_id' => $postCatId],
                ['order' => 0]
            );
        }

        // 6. Pages
        $pagesData = [
            [
                'title' => 'Giới thiệu về chúng tôi',
                'slug' => 'gioi-thieu',
                'content' => 'Chúng tôi là đơn vị hàng đầu cung cấp các giải pháp và thiết bị công nghệ chính hãng tại Việt Nam.',
            ],
            [
                'title' => 'Chính sách bảo hành',
                'slug' => 'chinh-sach-bao-hanh',
                'content' => 'Tất cả sản phẩm được bán ra đều cam kết bảo hành chính hãng từ 12 đến 24 tháng.',
            ],
            [
                'title' => 'Liên hệ hỗ trợ',
                'slug' => 'lien-he',
                'content' => 'Địa chỉ: 123 Đường ABC, Quận 1, TP. Hồ Chí Minh. Hotline: 1900 1234. Email: support@example.com',
            ],
        ];

        foreach ($pagesData as $pageData) {
            Post::updateOrCreate(
                ['slug' => $pageData['slug']],
                [
                    'project_id' => $projectId,
                    'tenant_id' => $projectId,
                    'title' => $pageData['title'],
                    'content' => $pageData['content'],
                    'post_type' => 'page',
                    'status' => 'published',
                ]
            );
        }

        // 7. Orders
        $customers = [
            ['name' => 'Nguyễn Văn An', 'email' => 'nguyenvanan@example.com', 'phone' => '0901234567', 'status' => 'delivered', 'pay_status' => 'paid'],
            ['name' => 'Trần Thị Bích', 'email' => 'tranthibich@example.com', 'phone' => '0912345678', 'status' => 'processing', 'pay_status' => 'paid'],
            ['name' => 'Lê Hoàng Nam', 'email' => 'lehoangnam@example.com', 'phone' => '0987654321', 'status' => 'pending', 'pay_status' => 'pending'],
            ['name' => 'Phạm Quốc Cường', 'email' => 'phamquoccuong@example.com', 'phone' => '0978123456', 'status' => 'shipped', 'pay_status' => 'paid'],
        ];

        foreach ($customers as $idx => $cust) {
            $orderNum = 'ORD-'.strtoupper(Str::random(6));
            $productSample = $createdProducts[$idx % count($createdProducts)];
            $metaData = is_array($productSample->meta_data) ? $productSample->meta_data : json_decode($productSample->meta_data, true);
            $unitPrice = $metaData['sale_price'] ?? $metaData['price'] ?? 25000000;
            $qty = rand(1, 2);
            $total = $unitPrice * $qty;

            $order = Order::create([
                'project_id' => $projectId,
                'tenant_id' => $projectId,
                'order_number' => $orderNum,
                'status' => $cust['status'],
                'subtotal' => $total,
                'shipping_amount' => 30000,
                'discount_amount' => 0,
                'total_amount' => $total + 30000,
                'currency' => 'VND',
                'customer_name' => $cust['name'],
                'customer_email' => $cust['email'],
                'customer_phone' => $cust['phone'],
                'shipping_address' => [
                    'address' => '123 Nguyễn Trãi, Phường Bến Thành',
                    'city' => 'Hồ Chí Minh',
                    'district' => 'Quận 1',
                ],
                'payment_method' => 'bank_transfer',
                'payment_status' => $cust['pay_status'],
                'paid_at' => $cust['pay_status'] === 'paid' ? now() : null,
            ]);

            OrderItem::create([
                'project_id' => $projectId,
                'tenant_id' => $projectId,
                'order_id' => $order->id,
                'product_id' => $productSample->id,
                'product_name' => $productSample->title,
                'product_sku' => $metaData['sku'] ?? 'SKU-DEMO',
                'unit_price' => $unitPrice,
                'quantity' => $qty,
                'total_price' => $total,
            ]);
        }

        if ($this->command) {
            $this->command->info('CMS Admin sample data seeded successfully!');
        }
    }
}
