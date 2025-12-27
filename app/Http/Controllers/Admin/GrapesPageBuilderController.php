<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class GrapesPageBuilderController extends Controller
{
    /**
     * Get or create homepage and show editor directly
     */
    public function index()
    {
        $homepage = $this->getOrCreateHomepage();
        return view('cms.grapes-builder.editor', ['page' => $homepage]);
    }

    /**
     * Get or create homepage
     */
    protected function getOrCreateHomepage(): Page
    {
        // Try to find existing homepage
        $homepage = Page::withoutGlobalScopes()
            ->where('slug', 'home')
            ->where('post_type', 'page')
            ->first();

        // Create if not exists
        if (!$homepage) {
            $homepage = Page::create([
                'title' => 'Trang chủ',
                'slug' => 'home',
                'template' => 'default',
                'content' => '',
                'post_type' => 'page',
                'grapes_data' => json_encode(['html' => '', 'css' => '', 'components' => [], 'styles' => []]),
                'status' => 'published',
            ]);
        }

        return $homepage;
    }

    /**
     * Save homepage content from GrapesJS
     */
    public function save(Request $request)
    {
        $homepage = $this->getOrCreateHomepage();
        
        $validated = $request->validate([
            'html' => 'nullable|string',
            'css' => 'nullable|string',
            'components' => 'nullable',
            'styles' => 'nullable',
        ]);

        $homepage->update([
            'content' => $validated['html'] ?? '',
            'custom_css' => $validated['css'] ?? '',
            'grapes_data' => json_encode([
                'html' => $validated['html'] ?? '',
                'css' => $validated['css'] ?? '',
                'components' => $validated['components'] ?? [],
                'styles' => $validated['styles'] ?? [],
            ]),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã lưu thành công!'
        ]);
    }

    /**
     * Load homepage content for GrapesJS
     */
    public function load()
    {
        $homepage = $this->getOrCreateHomepage();
        $grapesData = json_decode($homepage->grapes_data, true) ?? [];

        return response()->json([
            'success' => true,
            'data' => [
                'html' => $grapesData['html'] ?? $homepage->content ?? '',
                'css' => $grapesData['css'] ?? $homepage->custom_css ?? '',
                'components' => $grapesData['components'] ?? [],
                'styles' => $grapesData['styles'] ?? [],
            ]
        ]);
    }

    /**
     * Upload asset (image)
     */
    public function uploadAsset(Request $request)
    {
        $request->validate([
            'files.*' => 'required|image|max:5120',
        ]);

        $uploaded = [];
        $project = request()->attributes->get('project');
        $projectCode = $project ? $project->code : 'default';

        foreach ($request->file('files', []) as $file) {
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs("media/project-{$projectCode}/pagebuilder", $filename, 'public');
            
            $uploaded[] = [
                'src' => "/storage/{$path}",
                'type' => 'image',
                'name' => $file->getClientOriginalName(),
            ];
        }

        return response()->json(['data' => $uploaded]);
    }

    /**
     * Preview homepage
     */
    public function preview()
    {
        $homepage = $this->getOrCreateHomepage();
        return view('cms.grapes-builder.preview', ['page' => $homepage]);
    }

    /**
     * Get default blocks for GrapesJS
     */
    public function getDefaultBlocks(): array
    {
        $projectCode = request()->route('projectCode') ?? 'default';
        
        return [
            ...$this->getDynamicBlocks($projectCode),
            ...$this->getLayoutBlocks(),
        ];
    }

    /**
     * Dynamic blocks that load real data
     */
    protected function getDynamicBlocks(string $projectCode): array
    {
        return [
            [
                'id' => 'products-grid',
                'label' => '🛍️ Sản phẩm mới',
                'category' => 'Nội dung động',
                'content' => '<div data-widget="products" data-limit="8" class="py-12"><div class="container mx-auto px-4"><h2 class="text-3xl font-bold text-center mb-8">Sản phẩm mới nhất</h2><div class="grid grid-cols-2 md:grid-cols-4 gap-6"><div class="bg-white rounded-lg shadow p-4 text-center"><div class="bg-gray-200 h-48 rounded mb-4"></div><h3 class="font-semibold">Sản phẩm 1</h3><p class="text-red-600 font-bold">100.000đ</p></div><div class="bg-white rounded-lg shadow p-4 text-center"><div class="bg-gray-200 h-48 rounded mb-4"></div><h3 class="font-semibold">Sản phẩm 2</h3><p class="text-red-600 font-bold">200.000đ</p></div></div><div class="text-center mt-8"><a href="/' . $projectCode . '/san-pham" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Xem tất cả →</a></div></div></div>',
            ],
            [
                'id' => 'products-featured',
                'label' => '⭐ Sản phẩm nổi bật',
                'category' => 'Nội dung động',
                'content' => '<div data-widget="products-featured" class="py-12 bg-gray-50"><div class="container mx-auto px-4"><h2 class="text-3xl font-bold text-center mb-8">⭐ Sản phẩm nổi bật</h2><div class="grid grid-cols-2 md:grid-cols-4 gap-6"><div class="bg-white rounded-lg shadow p-4 text-center relative"><span class="absolute top-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded">Nổi bật</span><div class="bg-gray-200 h-48 rounded mb-4"></div><h3 class="font-semibold">Sản phẩm</h3><p class="text-red-600 font-bold">500.000đ</p></div></div></div></div>',
            ],
            [
                'id' => 'categories-grid',
                'label' => '📂 Danh mục',
                'category' => 'Nội dung động',
                'content' => '<div data-widget="categories" class="py-12 bg-white"><div class="container mx-auto px-4"><h2 class="text-3xl font-bold text-center mb-8">Danh mục sản phẩm</h2><div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4"><a href="#" class="block p-4 bg-gray-50 rounded-lg text-center hover:shadow-lg transition"><div class="w-16 h-16 bg-blue-100 rounded-full mx-auto mb-3 flex items-center justify-center"><span class="text-2xl">📱</span></div><span class="font-medium">Danh mục 1</span></a></div></div></div>',
            ],
            [
                'id' => 'banner-slider',
                'label' => '🖼️ Banner Slider',
                'category' => 'Nội dung động',
                'content' => '<div data-widget="slider" class="relative"><img src="https://via.placeholder.com/1920x500" alt="Banner" class="w-full h-[400px] object-cover"/></div>',
            ],
            [
                'id' => 'posts-latest',
                'label' => '📰 Bài viết mới',
                'category' => 'Nội dung động',
                'content' => '<div data-widget="posts" class="py-12 bg-gray-50"><div class="container mx-auto px-4"><h2 class="text-3xl font-bold text-center mb-8">Tin tức mới nhất</h2><div class="grid md:grid-cols-3 gap-8"><article class="bg-white rounded-lg shadow overflow-hidden"><img src="https://via.placeholder.com/400x250" alt="Post" class="w-full h-48 object-cover"/><div class="p-6"><span class="text-sm text-gray-500">26/12/2024</span><h3 class="text-xl font-bold mt-2 mb-3">Tiêu đề bài viết</h3><p class="text-gray-600 mb-4">Mô tả ngắn...</p><a href="#" class="text-blue-600 font-semibold hover:underline">Đọc thêm →</a></div></article></div></div></div>',
            ],
        ];
    }

    protected function getLayoutBlocks(): array
    {
        return [
            [
                'id' => 'section',
                'label' => '📦 Khung chứa',
                'category' => 'Bố cục',
                'content' => '<section class="py-16 px-4"><div class="container mx-auto">Nội dung khung chứa</div></section>',
                'attributes' => ['class' => 'gjs-block-section'],
            ],
            [
                'id' => 'container',
                'label' => '📐 Container',
                'category' => 'Bố cục',
                'content' => '<div class="container mx-auto px-4">Nội dung container</div>',
            ],
            [
                'id' => 'row',
                'label' => '⬜ 2 Cột',
                'category' => 'Bố cục',
                'content' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-6"><div class="p-4 bg-gray-100 rounded">Cột 1</div><div class="p-4 bg-gray-100 rounded">Cột 2</div></div>',
            ],
            [
                'id' => 'row-3',
                'label' => '⬜ 3 Cột',
                'category' => 'Bố cục',
                'content' => '<div class="grid grid-cols-1 md:grid-cols-3 gap-6"><div class="p-4 bg-gray-100 rounded">Cột 1</div><div class="p-4 bg-gray-100 rounded">Cột 2</div><div class="p-4 bg-gray-100 rounded">Cột 3</div></div>',
            ],
            [
                'id' => 'row-4',
                'label' => '⬜ 4 Cột',
                'category' => 'Bố cục',
                'content' => '<div class="grid grid-cols-2 md:grid-cols-4 gap-4"><div class="p-4 bg-gray-100 rounded">1</div><div class="p-4 bg-gray-100 rounded">2</div><div class="p-4 bg-gray-100 rounded">3</div><div class="p-4 bg-gray-100 rounded">4</div></div>',
            ],

            // Khối cơ bản
            [
                'id' => 'text',
                'label' => '📝 Văn bản',
                'category' => 'Cơ bản',
                'content' => '<p class="text-gray-700">Nhập nội dung văn bản tại đây...</p>',
            ],
            [
                'id' => 'heading',
                'label' => '🔤 Tiêu đề',
                'category' => 'Cơ bản',
                'content' => '<h2 class="text-3xl font-bold text-gray-900 mb-4">Tiêu đề</h2>',
            ],
            [
                'id' => 'image',
                'label' => '🖼️ Hình ảnh',
                'category' => 'Cơ bản',
                'content' => '<img src="https://via.placeholder.com/800x400" alt="Hình ảnh" class="w-full rounded-lg shadow"/>',
                'activate' => true,
            ],
            [
                'id' => 'button',
                'label' => '🔘 Nút bấm',
                'category' => 'Cơ bản',
                'content' => '<a href="#" class="inline-block px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">Nhấn vào đây</a>',
            ],
            [
                'id' => 'link',
                'label' => '🔗 Liên kết',
                'category' => 'Cơ bản',
                'content' => '<a href="#" class="text-blue-600 hover:underline">Văn bản liên kết</a>',
            ],
            [
                'id' => 'divider',
                'label' => '➖ Đường kẻ',
                'category' => 'Cơ bản',
                'content' => '<hr class="my-8 border-gray-300"/>',
            ],

            // Phần Hero
            [
                'id' => 'hero-1',
                'label' => '🎯 Banner đơn giản',
                'category' => 'Banner',
                'content' => '
                    <section class="bg-gradient-to-r from-blue-600 to-purple-600 py-20">
                        <div class="container mx-auto px-4 text-center text-white">
                            <h1 class="text-5xl font-bold mb-6">Chào mừng đến với Website</h1>
                            <p class="text-xl mb-8 opacity-90">Khám phá sản phẩm và dịch vụ tuyệt vời</p>
                            <a href="#" class="inline-block px-8 py-4 bg-white text-blue-600 font-bold rounded-lg hover:bg-gray-100 transition">Bắt đầu ngay</a>
                        </div>
                    </section>
                ',
            ],
            [
                'id' => 'hero-2',
                'label' => '🎯 Banner có hình',
                'category' => 'Banner',
                'content' => '
                    <section class="py-20 bg-gray-50">
                        <div class="container mx-auto px-4">
                            <div class="grid md:grid-cols-2 gap-12 items-center">
                                <div>
                                    <h1 class="text-4xl font-bold text-gray-900 mb-6">Xây dựng điều tuyệt vời</h1>
                                    <p class="text-lg text-gray-600 mb-8">Mô tả ngắn gọn về sản phẩm hoặc dịch vụ của bạn. Hãy thay đổi nội dung này theo ý muốn.</p>
                                    <div class="flex gap-4">
                                        <a href="#" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Nút chính</a>
                                        <a href="#" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-100">Nút phụ</a>
                                    </div>
                                </div>
                                <div>
                                    <img src="https://via.placeholder.com/600x400" alt="Banner" class="rounded-lg shadow-xl"/>
                                </div>
                            </div>
                        </div>
                    </section>
                ',
            ],

            // Tính năng
            [
                'id' => 'features-3',
                'label' => '✨ Tính năng (3 cột)',
                'category' => 'Tính năng',
                'content' => '
                    <section class="py-16 bg-white">
                        <div class="container mx-auto px-4">
                            <h2 class="text-3xl font-bold text-center mb-12">Tính năng nổi bật</h2>
                            <div class="grid md:grid-cols-3 gap-8">
                                <div class="text-center p-6">
                                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    </div>
                                    <h3 class="text-xl font-bold mb-2">Tính năng 1</h3>
                                    <p class="text-gray-600">Mô tả tính năng đầu tiên của bạn.</p>
                                </div>
                                <div class="text-center p-6">
                                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <h3 class="text-xl font-bold mb-2">Tính năng 2</h3>
                                    <p class="text-gray-600">Mô tả tính năng thứ hai của bạn.</p>
                                </div>
                                <div class="text-center p-6">
                                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                    </div>
                                    <h3 class="text-xl font-bold mb-2">Tính năng 3</h3>
                                    <p class="text-gray-600">Mô tả tính năng thứ ba của bạn.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                ',
            ],

            // Thẻ
            [
                'id' => 'card',
                'label' => '🃏 Thẻ nội dung',
                'category' => 'Thẻ',
                'content' => '
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden max-w-sm">
                        <img src="https://via.placeholder.com/400x200" alt="Thẻ" class="w-full h-48 object-cover"/>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-2">Tiêu đề thẻ</h3>
                            <p class="text-gray-600 mb-4">Mô tả nội dung thẻ. Thêm chi tiết về mục này.</p>
                            <a href="#" class="text-blue-600 font-semibold hover:underline">Xem thêm →</a>
                        </div>
                    </div>
                ',
            ],
            [
                'id' => 'pricing-card',
                'label' => '💰 Thẻ bảng giá',
                'category' => 'Thẻ',
                'content' => '
                    <div class="bg-white rounded-lg shadow-lg p-8 text-center max-w-sm">
                        <h3 class="text-xl font-bold mb-2">Gói Pro</h3>
                        <div class="text-4xl font-bold text-blue-600 mb-4">500K<span class="text-lg text-gray-500">/tháng</span></div>
                        <ul class="text-gray-600 mb-6 space-y-2">
                            <li>✓ Tính năng một</li>
                            <li>✓ Tính năng hai</li>
                            <li>✓ Tính năng ba</li>
                            <li>✓ Tính năng bốn</li>
                        </ul>
                        <a href="#" class="block w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Đăng ký ngay</a>
                    </div>
                ',
            ],

            // Đánh giá
            [
                'id' => 'testimonial',
                'label' => '💬 Đánh giá khách hàng',
                'category' => 'Đánh giá',
                'content' => '
                    <div class="bg-gray-50 rounded-lg p-8 max-w-lg">
                        <div class="flex items-center mb-4">
                            <img src="https://via.placeholder.com/60" alt="Avatar" class="w-12 h-12 rounded-full mr-4"/>
                            <div>
                                <h4 class="font-bold">Nguyễn Văn A</h4>
                                <p class="text-gray-500 text-sm">Giám đốc, Công ty ABC</p>
                            </div>
                        </div>
                        <p class="text-gray-700 italic">"Đây là sản phẩm tuyệt vời! Nó đã hoàn toàn thay đổi cách chúng tôi kinh doanh. Rất khuyến khích sử dụng."</p>
                        <div class="mt-4 text-yellow-400">★★★★★</div>
                    </div>
                ',
            ],

            // Kêu gọi hành động
            [
                'id' => 'cta-1',
                'label' => '📢 Banner kêu gọi',
                'category' => 'Kêu gọi',
                'content' => '
                    <section class="bg-blue-600 py-16">
                        <div class="container mx-auto px-4 text-center">
                            <h2 class="text-3xl font-bold text-white mb-4">Sẵn sàng bắt đầu?</h2>
                            <p class="text-blue-100 mb-8">Tham gia cùng hàng ngàn khách hàng hài lòng ngay hôm nay.</p>
                            <a href="#" class="inline-block px-8 py-4 bg-white text-blue-600 font-bold rounded-lg hover:bg-gray-100 transition">Dùng thử miễn phí</a>
                        </div>
                    </section>
                ',
            ],

            // Biểu mẫu liên hệ
            [
                'id' => 'contact-form',
                'label' => '📧 Form liên hệ',
                'category' => 'Biểu mẫu',
                'content' => '
                    <section class="py-16 bg-gray-50">
                        <div class="container mx-auto px-4 max-w-2xl">
                            <h2 class="text-3xl font-bold text-center mb-8">Liên hệ với chúng tôi</h2>
                            <form class="space-y-6">
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Họ tên</label>
                                        <input type="text" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Nhập họ tên"/>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Email</label>
                                        <input type="email" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="email@example.com"/>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">Nội dung</label>
                                    <textarea rows="4" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Nhập nội dung tin nhắn..."></textarea>
                                </div>
                                <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700">Gửi tin nhắn</button>
                            </form>
                        </div>
                    </section>
                ',
            ],

            // Chân trang
            [
                'id' => 'footer',
                'label' => '📋 Chân trang',
                'category' => 'Chân trang',
                'content' => '
                    <footer class="bg-gray-900 text-white py-12">
                        <div class="container mx-auto px-4">
                            <div class="grid md:grid-cols-4 gap-8">
                                <div>
                                    <h3 class="text-xl font-bold mb-4">Công ty</h3>
                                    <p class="text-gray-400">Xây dựng sản phẩm tuyệt vời cho khách hàng.</p>
                                </div>
                                <div>
                                    <h4 class="font-bold mb-4">Liên kết</h4>
                                    <ul class="space-y-2 text-gray-400">
                                        <li><a href="#" class="hover:text-white">Trang chủ</a></li>
                                        <li><a href="#" class="hover:text-white">Giới thiệu</a></li>
                                        <li><a href="#" class="hover:text-white">Dịch vụ</a></li>
                                        <li><a href="#" class="hover:text-white">Liên hệ</a></li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-bold mb-4">Hỗ trợ</h4>
                                    <ul class="space-y-2 text-gray-400">
                                        <li><a href="#" class="hover:text-white">Câu hỏi thường gặp</a></li>
                                        <li><a href="#" class="hover:text-white">Trung tâm trợ giúp</a></li>
                                        <li><a href="#" class="hover:text-white">Chính sách bảo mật</a></li>
                                        <li><a href="#" class="hover:text-white">Điều khoản sử dụng</a></li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-bold mb-4">Liên hệ</h4>
                                    <ul class="space-y-2 text-gray-400">
                                        <li>📧 info@congty.com</li>
                                        <li>📞 0123 456 789</li>
                                        <li>📍 123 Đường ABC, Quận 1, TP.HCM</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                                <p>© 2024 Công ty. Bảo lưu mọi quyền.</p>
                            </div>
                        </div>
                    </footer>
                ',
            ],
        ];
    }
}
