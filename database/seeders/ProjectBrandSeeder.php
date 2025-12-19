<?php

namespace Database\Seeders;

use App\Models\ProjectBrand;
use Illuminate\Database\Seeder;

class ProjectBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        \DB::connection('project')->statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing data
        ProjectBrand::truncate();

        $brands = [
            [
                'name' => 'Apple',
                'slug' => 'apple',
                'description' => 'Thương hiệu công nghệ hàng đầu thế giới',
                'website' => 'https://apple.com',
            ],
            [
                'name' => 'Samsung',
                'slug' => 'samsung',
                'description' => 'Thương hiệu điện tử Hàn Quốc',
                'website' => 'https://samsung.com',
            ],
            [
                'name' => 'Nike',
                'slug' => 'nike',
                'description' => 'Thương hiệu thể thao nổi tiếng',
                'website' => 'https://nike.com',
            ],
            [
                'name' => 'Adidas',
                'slug' => 'adidas',
                'description' => 'Thương hiệu thể thao Đức',
                'website' => 'https://adidas.com',
            ],
            [
                'name' => 'Zara',
                'slug' => 'zara',
                'description' => 'Thương hiệu thời trang Tây Ban Nha',
                'website' => 'https://zara.com',
            ],
            [
                'name' => 'H&M',
                'slug' => 'hm',
                'description' => 'Thương hiệu thời trang Thụy Điển',
                'website' => 'https://hm.com',
            ],
            [
                'name' => 'Uniqlo',
                'slug' => 'uniqlo',
                'description' => 'Thương hiệu thời trang Nhật Bản',
                'website' => 'https://uniqlo.com',
            ],
            [
                'name' => 'Sony',
                'slug' => 'sony',
                'description' => 'Thương hiệu điện tử Nhật Bản',
                'website' => 'https://sony.com',
            ],
            [
                'name' => 'LG',
                'slug' => 'lg',
                'description' => 'Thương hiệu điện tử Hàn Quốc',
                'website' => 'https://lg.com',
            ],
            [
                'name' => 'IKEA',
                'slug' => 'ikea',
                'description' => 'Thương hiệu nội thất Thụy Điển',
                'website' => 'https://ikea.com',
            ],
        ];

        foreach ($brands as $brandData) {
            ProjectBrand::create([
                'name' => $brandData['name'],
                'slug' => $brandData['slug'],
                'description' => $brandData['description'],
                'is_active' => true,
            ]);
        }

        // Re-enable foreign key checks
        \DB::connection('project')->statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✓ Created '.ProjectBrand::count().' project brands');
    }
}
