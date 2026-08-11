<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TailwindContentSeeder extends Seeder
{
    public function run()
    {
        $project = Project::where('code', 'HD001')->first();
        if (! $project) {
            $project = Project::first();
        }

        $projectId = session('current_project_id') ?? ($project ? $project->id : 1);
        $tenantId = session('current_tenant_id') ?? $projectId;

        // Đảm bảo tenant tồn tại để không lỗi foreign key
        if (! DB::table('tenants')->where('id', $tenantId)->exists()) {
            DB::table('tenants')->insertOrIgnore([
                'id' => $tenantId,
                'name' => 'Project Tenant '.$tenantId,
                'domain' => 'tenant'.$tenantId.'.test',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $posts = [
            // ==========================================
            // DEMO PAGE (Trang Chủ Demo)
            // ==========================================
            [
                'title' => 'Trang Chủ Tailwind Demo',
                'slug' => 'demo-page',
                'excerpt' => 'Trang chủ mẫu được thiết kế bằng Tailwind CSS',
                'content' => '
<!-- Hero Section -->
<div class="relative bg-gray-900 overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <div class="relative z-10 pb-8 bg-gray-900 sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
            <svg class="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-gray-900 transform translate-x-1/2" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                <polygon points="50,0 100,0 50,100 0,100" />
            </svg>
            <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                <div class="sm:text-center lg:text-left">
                    <h1 class="text-4xl tracking-tight font-extrabold text-white sm:text-5xl md:text-6xl">
                        <span class="block xl:inline">Giao diện chuyên nghiệp</span>
                        <span class="block text-indigo-500 xl:inline">với Tailwind CSS</span>
                    </h1>
                    <p class="mt-3 text-base text-gray-300 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                        Khám phá các layout tuyệt đẹp được xây dựng sẵn. Dễ dàng tùy biến, tối ưu hóa trải nghiệm người dùng và đạt hiệu suất cao nhất cho website của bạn.
                    </p>
                    <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                        <div class="rounded-md shadow">
                            <a href="#" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10"> Bắt đầu ngay </a>
                        </div>
                        <div class="mt-3 sm:mt-0 sm:ml-3">
                            <a href="#" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 md:py-4 md:text-lg md:px-10"> Xem tính năng </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
        <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" src="https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2850&q=80" alt="">
    </div>
</div>

<!-- Features Section -->
<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center">
            <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Tính năng nổi bật</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">Trải nghiệm tuyệt vời hơn</p>
            <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">Mọi thứ bạn cần để xây dựng một website mạnh mẽ đều được tích hợp sẵn trong bộ công cụ của chúng tôi.</p>
        </div>

        <div class="mt-10">
            <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
                <div class="relative">
                    <dt>
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                        </div>
                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Chuẩn SEO toàn cầu</p>
                    </dt>
                    <dd class="mt-2 ml-16 text-base text-gray-500">Tối ưu hóa công cụ tìm kiếm giúp website của bạn luôn nằm ở top đầu của Google.</dd>
                </div>

                <div class="relative">
                    <dt>
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Tốc độ cực nhanh</p>
                    </dt>
                    <dd class="mt-2 ml-16 text-base text-gray-500">Tải trang trong chớp mắt nhờ công nghệ cache và CDN tiên tiến nhất.</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
',
                'post_type' => 'page',
                'status' => 'published',
            ],

            // ==========================================
            // DEMO POST 1 (Bài viết Demo)
            // ==========================================
            [
                'title' => 'Khám phá kiến trúc Tailwind CSS hiện đại',
                'slug' => 'demo-post-tailwind',
                'excerpt' => 'Bài viết mẫu với typography chuẩn của Tailwind.',
                'content' => '
<article class="prose prose-lg prose-indigo mx-auto mt-6">
    <p class="lead">Tailwind CSS đã thay đổi hoàn toàn cách chúng ta xây dựng giao diện web. Thay vì phải viết hàng ngàn dòng CSS tùy chỉnh, giờ đây bạn có thể sử dụng các utility classes để định hình mọi thứ một cách nhanh chóng.</p>
    
    <h2>Tại sao chọn Tailwind?</h2>
    <p>Tailwind mang lại cho bạn khả năng kiểm soát tốt hơn đối với thiết kế. Bằng cách áp dụng các classes như <code>flex</code>, <code>pt-4</code>, <code>text-center</code>, hay <code>rotate-90</code>, bạn có thể thiết kế trực tiếp trong HTML.</p>
    
    <blockquote>
        <p>"Tailwind CSS không chỉ là một framework, nó là một tư duy thiết kế."</p>
    </blockquote>
    
    <h3>Lợi ích chính:</h3>
    <ul>
        <li>Không phải đặt tên class tốn thời gian.</li>
        <li>Kích thước file CSS cực nhỏ sau khi build.</li>
        <li>Dễ dàng tạo responsive design.</li>
    </ul>

    <div class="my-8 rounded-lg overflow-hidden shadow-lg">
        <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="Code example" class="w-full object-cover">
    </div>

    <p>Bằng cách kết hợp linh hoạt, giao diện website trở nên sống động và mang lại cảm giác cực kỳ chuyên nghiệp.</p>
</article>
',
                'post_type' => 'post',
                'status' => 'published',
            ],

            // ==========================================
            // DEMO POST 2
            // ==========================================
            [
                'title' => 'Tương lai của thiết kế Web năm 2024',
                'slug' => 'demo-post-web-design',
                'excerpt' => 'Xu hướng thiết kế UI/UX mới nhất trong năm 2024.',
                'content' => '
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-8">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-indigo-700">Đây là một ghi chú quan trọng về bài viết này.</p>
            </div>
        </div>
    </div>
    
    <h2 class="text-2xl font-bold text-gray-900 mb-4">Dark Mode đang thống trị</h2>
    <p class="text-gray-700 leading-relaxed mb-6">Người dùng ngày càng ưu chuộng giao diện tối vì nó giảm mỏi mắt và tiết kiệm pin cho thiết bị di động. Tailwind cung cấp tính năng dark mode tích hợp sẵn vô cùng mạnh mẽ.</p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-8">
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Thanh lịch</h3>
            <p class="text-gray-600">Sử dụng gam màu trung tính kết hợp với typography đậm nhạt rõ ràng.</p>
        </div>
        <div class="bg-gray-900 rounded-lg shadow p-6 border border-gray-700">
            <h3 class="text-lg font-semibold text-white mb-2">Huyền bí</h3>
            <p class="text-gray-400">Dark mode với độ tương phản cao, làm nổi bật các yếu tố quan trọng.</p>
        </div>
    </div>
</div>
',
                'post_type' => 'post',
                'status' => 'published',
            ],

            // ==========================================
            // DEMO PRODUCT (Sản phẩm Demo)
            // ==========================================
            [
                'title' => 'Sản phẩm Tailwind Demo',
                'slug' => 'demo-product-tailwind',
                'excerpt' => 'Sản phẩm mẫu với thiết kế Tailwind cực đẹp.',
                'content' => '
<div class="bg-white">
  <div class="pt-6 pb-16 sm:pb-24">
    <div class="mt-8 max-w-2xl mx-auto px-4 sm:px-6 lg:max-w-7xl lg:px-8">
      <div class="lg:grid lg:grid-cols-12 lg:auto-rows-min lg:gap-x-8">
        <div class="lg:col-start-8 lg:col-span-5">
          <div class="flex justify-between">
            <h1 class="text-xl font-medium text-gray-900">Sản phẩm chất lượng cao</h1>
            <p class="text-xl font-medium text-gray-900">$140</p>
          </div>
          <div class="mt-4">
            <h2 class="sr-only">Reviews</h2>
            <div class="flex items-center">
              <p class="text-sm text-gray-700">3.9
                <span class="sr-only"> out of 5 stars</span>
              </p>
              <div class="ml-1 flex items-center">
                <svg class="text-yellow-400 h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <!-- Add more stars as needed -->
              </div>
              <div aria-hidden="true" class="ml-4 text-sm text-gray-300">·</div>
              <div class="ml-4 flex">
                <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Xem tất cả 117 đánh giá</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Image gallery -->
        <div class="mt-8 lg:mt-0 lg:col-start-1 lg:col-span-7 lg:row-start-1 lg:row-span-3">
          <h2 class="sr-only">Images</h2>
          <div class="grid grid-cols-1 lg:grid-cols-2 lg:grid-rows-3 lg:gap-8">
            <img src="https://tailwindui.com/img/ecommerce-images/product-page-01-featured-product-shot.jpg" alt="Featured Image" class="lg:col-span-2 lg:row-span-2 rounded-lg">
          </div>
        </div>

        <div class="mt-8 lg:col-span-5">
          <form>
            <button type="submit" class="mt-8 w-full bg-indigo-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Thêm vào giỏ hàng</button>
          </form>
          <div class="mt-10">
            <h2 class="text-sm font-medium text-gray-900">Mô tả sản phẩm</h2>
            <div class="mt-4 prose prose-sm text-gray-500">
              <p>Sản phẩm này là một ví dụ về thiết kế chi tiết sản phẩm chuẩn UI/UX từ Tailwind. Bạn có thể sử dụng các components được xây dựng sẵn để làm cho trang web thương mại điện tử của bạn cực kỳ chuyên nghiệp và thu hút.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
',
                'post_type' => 'product',
                'status' => 'published',
            ],
        ];

        foreach ($posts as $post) {
            DB::table('posts')->updateOrInsert(
                ['slug' => $post['slug']],
                array_merge($post, [
                    'tenant_id' => $tenantId,
                    'project_id' => $projectId,
                    'published_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Đã tạo thành công seeder dữ liệu Tailwind mẫu!');
    }
}
