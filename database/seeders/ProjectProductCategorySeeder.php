<?php

namespace Database\Seeders;

use App\Models\ProjectProductCategory;
use Illuminate\Database\Seeder;

class ProjectProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        \DB::connection('project')->statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing data
        ProjectProductCategory::truncate();

        $categories = [
            [
                'name' => 'Thời trang',
                'slug' => 'thoi-trang',
                'description' => 'Danh mục thời trang',
                'children' => [
                    ['name' => 'Quần áo nam', 'slug' => 'quan-ao-nam'],
                    ['name' => 'Quần áo nữ', 'slug' => 'quan-ao-nu'],
                    ['name' => 'Quần áo trẻ em', 'slug' => 'quan-ao-tre-em'],
                    ['name' => 'Phụ kiện thời trang', 'slug' => 'phu-kien-thoi-trang'],
                ],
            ],
            [
                'name' => 'Điện tử',
                'slug' => 'dien-tu',
                'description' => 'Danh mục điện tử',
                'children' => [
                    ['name' => 'Điện thoại', 'slug' => 'dien-thoai'],
                    ['name' => 'Laptop', 'slug' => 'laptop'],
                    ['name' => 'Máy tính bảng', 'slug' => 'may-tinh-bang'],
                    ['name' => 'Phụ kiện điện tử', 'slug' => 'phu-kien-dien-tu'],
                ],
            ],
            [
                'name' => 'Gia dụng',
                'slug' => 'gia-dung',
                'description' => 'Danh mục gia dụng',
                'children' => [
                    ['name' => 'Đồ dùng nhà bếp', 'slug' => 'do-dung-nha-bep'],
                    ['name' => 'Đồ nội thất', 'slug' => 'do-noi-that'],
                    ['name' => 'Đồ trang trí', 'slug' => 'do-trang-tri'],
                ],
            ],
            [
                'name' => 'Thể thao',
                'slug' => 'the-thao',
                'description' => 'Danh mục thể thao',
                'children' => [
                    ['name' => 'Quần áo thể thao', 'slug' => 'quan-ao-the-thao'],
                    ['name' => 'Giày thể thao', 'slug' => 'giay-the-thao'],
                    ['name' => 'Dụng cụ thể thao', 'slug' => 'dung-cu-the-thao'],
                ],
            ],
            [
                'name' => 'Sách & Văn phòng phẩm',
                'slug' => 'sach-van-phong-pham',
                'description' => 'Danh mục sách và văn phòng phẩm',
                'children' => [
                    ['name' => 'Sách', 'slug' => 'sach'],
                    ['name' => 'Văn phòng phẩm', 'slug' => 'van-phong-pham'],
                ],
            ],
        ];

        $sortOrder = 1;
        foreach ($categories as $categoryData) {
            $parent = ProjectProductCategory::create([
                'name' => $categoryData['name'],
                'slug' => $categoryData['slug'],
                'description' => $categoryData['description'],
                'parent_id' => null,
                'sort_order' => $sortOrder++,
                'is_active' => true,
                'meta_title' => $categoryData['name'],
                'meta_description' => $categoryData['description'],
            ]);

            if (isset($categoryData['children'])) {
                $childSortOrder = 1;
                foreach ($categoryData['children'] as $childData) {
                    ProjectProductCategory::create([
                        'name' => $childData['name'],
                        'slug' => $childData['slug'],
                        'description' => 'Danh mục con: '.$childData['name'],
                        'parent_id' => $parent->id,
                        'sort_order' => $childSortOrder++,
                        'is_active' => true,
                        'meta_title' => $childData['name'],
                        'meta_description' => 'Danh mục con: '.$childData['name'],
                    ]);
                }
            }
        }

        // Re-enable foreign key checks
        \DB::connection('project')->statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✓ Created '.ProjectProductCategory::count().' project product categories');
    }
}
