<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Project;

class TailwindHeaderSeeder extends Seeder
{
    public function run()
    {
        $project = Project::where('code', 'HD001')->first();
        if (!$project) {
            $project = Project::first();
        }

        $projectId = session('current_project_id') ?? ($project ? $project->id : 1);
        $tenantId = session('current_tenant_id') ?? $projectId;

        if (!DB::table('tenants')->where('id', $tenantId)->exists()) {
            DB::table('tenants')->insertOrIgnore([
                'id' => $tenantId,
                'name' => 'Project Tenant ' . $tenantId,
                'domain' => 'tenant' . $tenantId . '.test',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $headers = [
            [
                'title' => 'Header Style 1',
                'slug' => 'header-style-1',
                'excerpt' => 'Logo trái, menu giữa, icons phải',
                'featured_image' => '/images/header/header-style-1.png',
                'content' => '
<header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex-shrink-0 flex items-center">
                <a href="/" class="text-2xl font-bold text-indigo-600">Logo</a>
            </div>
            <nav class="hidden md:flex space-x-8">
                <a href="#" class="text-gray-900 font-medium">Trang chủ</a>
                <a href="#" class="text-gray-500 hover:text-gray-900 font-medium">Sản phẩm</a>
                <a href="#" class="text-gray-500 hover:text-gray-900 font-medium">Tin tức</a>
                <a href="#" class="text-gray-500 hover:text-gray-900 font-medium">Liên hệ</a>
            </nav>
            <div class="flex items-center space-x-4">
                <button class="text-gray-500 hover:text-indigo-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <button class="text-gray-500 hover:text-indigo-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </button>
            </div>
        </div>
    </div>
</header>
',
                'post_type' => 'header',
                'status' => 'published',
            ],
            [
                'title' => 'Header Style 2',
                'slug' => 'header-style-2',
                'excerpt' => '2 hàng: Logo + icons / Menu',
                'featured_image' => '/images/header/header-style-2.png',
                'content' => '
<header class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 border-b">
        <div class="flex justify-between items-center h-20">
            <div class="flex-shrink-0 flex items-center">
                <a href="/" class="text-3xl font-extrabold text-gray-900">Brand</a>
            </div>
            <div class="flex items-center space-x-6">
                <div class="hidden sm:flex text-sm text-gray-500">Hotline: 1900 1234</div>
                <button class="bg-indigo-600 text-white px-4 py-2 rounded text-sm font-medium hover:bg-indigo-700">Tư vấn ngay</button>
            </div>
        </div>
    </div>
    <div class="bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex space-x-8 py-3">
                <a href="#" class="text-indigo-600 font-medium">Trang chủ</a>
                <a href="#" class="text-gray-600 hover:text-indigo-600 font-medium">Về chúng tôi</a>
                <a href="#" class="text-gray-600 hover:text-indigo-600 font-medium">Dịch vụ</a>
                <a href="#" class="text-gray-600 hover:text-indigo-600 font-medium">Dự án</a>
            </nav>
        </div>
    </div>
</header>
',
                'post_type' => 'header',
                'status' => 'published',
            ],
            [
                'title' => 'Header Style 3',
                'slug' => 'header-style-3',
                'excerpt' => 'Logo trái, menu + search phải',
                'featured_image' => '/images/header/header-style-1-1.png',
                'content' => '
<header class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex-shrink-0 flex items-center">
                <a href="/" class="text-2xl font-bold tracking-wider">STORE</a>
            </div>
            <div class="flex items-center space-x-6">
                <nav class="hidden md:flex space-x-6">
                    <a href="#" class="hover:text-gray-300">Home</a>
                    <a href="#" class="hover:text-gray-300">Shop</a>
                    <a href="#" class="hover:text-gray-300">Collections</a>
                </nav>
                <div class="relative">
                    <input type="text" placeholder="Search..." class="bg-gray-800 text-sm text-white rounded-full px-4 py-1 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>
    </div>
</header>
',
                'post_type' => 'header',
                'status' => 'published',
            ]
        ];

        foreach ($headers as $header) {
            DB::table('posts')->updateOrInsert(
                ['slug' => $header['slug']],
                array_merge($header, [
                    'tenant_id' => $tenantId,
                    'project_id' => $projectId,
                    'published_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
        
        $this->command->info('Đã tạo thành công seeder dữ liệu Tailwind Header mẫu!');
    }
}
