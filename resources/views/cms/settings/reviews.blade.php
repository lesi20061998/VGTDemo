@extends('cms.layouts.app')

@section('title', 'Cấu hình đánh giá')
@section('page-title', 'Đánh giá & Rating')

@section('content')
<div class="mb-6">
    <a href="{{ route('cms.settings.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Quay lại</a>
</div>

<div class="bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('cms.settings.save') }}" method="POST">
        @csrf
        
        <div class="space-y-6">
            @php
                $reviews = json_decode(setting('reviews', '{}'), true);
                $enabled = $reviews['enabled'] ?? true;
                $requireLogin = $reviews['require_login'] ?? true;
                $requirePurchase = $reviews['require_purchase'] ?? false;
                $autoApprove = $reviews['auto_approve'] ?? false;
                $allowImages = $reviews['allow_images'] ?? true;
                $maxImages = $reviews['max_images'] ?? 5;
                $minRating = $reviews['min_rating'] ?? 1;
                $showVerified = $reviews['show_verified'] ?? true;
                $rewardPoints = $reviews['reward_points'] ?? 0;
            @endphp

            <!-- Enable Reviews -->
            <div class="border-b pb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="reviews[enabled]" value="1" {{ $enabled ? 'checked' : '' }} class="mr-2 rounded">
                    <span class="font-medium text-lg">Bật chức năng đánh giá</span>
                </label>
                <p class="text-sm text-gray-500 mt-1 ml-6">Cho phép khách hàng đánh giá sản phẩm</p>
            </div>

            <!-- Review Requirements -->
            <div>
                <h3 class="font-semibold mb-3">Yêu cầu đánh giá</h3>
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="reviews[require_login]" value="1" {{ $requireLogin ? 'checked' : '' }} class="mr-2 rounded">
                        <span class="text-sm">Yêu cầu đăng nhập</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="reviews[require_purchase]" value="1" {{ $requirePurchase ? 'checked' : '' }} class="mr-2 rounded">
                        <span class="text-sm">Chỉ khách đã mua hàng mới được đánh giá</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="reviews[show_verified]" value="1" {{ $showVerified ? 'checked' : '' }} class="mr-2 rounded">
                        <span class="text-sm">Hiển thị badge "Đã mua hàng"</span>
                    </label>
                </div>
            </div>

            <!-- Approval -->
            <div>
                <h3 class="font-semibold mb-3">Kiểm duyệt</h3>
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="reviews[auto_approve]" value="1" {{ $autoApprove ? 'checked' : '' }} class="mr-2 rounded">
                        <span class="text-sm">Tự động duyệt đánh giá</span>
                    </label>
                    <p class="text-xs text-gray-500 ml-6">Nếu tắt, admin phải duyệt thủ công</p>
                    
                    <div>
                        <label class="block text-sm font-medium mb-2">Đối tượng áp dụng</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="reviews[enable_product]" value="1" {{ ($reviews['enable_product'] ?? true) ? 'checked' : '' }} class="mr-2 rounded">
                                <span class="text-sm">Sản phẩm</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="reviews[enable_post]" value="1" {{ ($reviews['enable_post'] ?? false) ? 'checked' : '' }} class="mr-2 rounded">
                                <span class="text-sm">Bài viết</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Display Position -->
            <div class="border-t pt-4">
                <h3 class="font-semibold mb-3">Vị trí hiển thị (Sản phẩm)</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Canh lề</label>
                        <select name="reviews[align]" class="w-full px-4 py-2 border rounded-lg">
                            <option value="left" {{ ($reviews['align'] ?? 'left') == 'left' ? 'selected' : '' }}>Canh trái</option>
                            <option value="center" {{ ($reviews['align'] ?? 'left') == 'center' ? 'selected' : '' }}>Canh giữa</option>
                            <option value="right" {{ ($reviews['align'] ?? 'left') == 'right' ? 'selected' : '' }}>Canh phải</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Số thứ tự hiển thị</label>
                        <input type="number" name="reviews[display_order]" value="{{ $reviews['display_order'] ?? 10 }}" min="1" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                </div>
            </div>
            
            <!-- Review Template -->
            <div class="border-t pt-4">
                <h3 class="font-semibold mb-3">Giao diện form đánh giá</h3>
                <div class="grid grid-cols-3 gap-4">
                    <label class="cursor-pointer">
                        <input type="radio" name="reviews[template]" value="template1" {{ ($reviews['template'] ?? 'template1') == 'template1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="border-2 rounded-lg p-4 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                            <img src="/assets/img/review-template-1.png" alt="Template 1" class="w-full mb-2 rounded">
                            <p class="text-sm font-medium text-center">Giao diện 1</p>
                            <p class="text-xs text-gray-500 text-center">Classic</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="reviews[template]" value="template2" {{ ($reviews['template'] ?? 'template1') == 'template2' ? 'checked' : '' }} class="sr-only peer">
                        <div class="border-2 rounded-lg p-4 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                            <img src="/assets/img/review-template-2.png" alt="Template 2" class="w-full mb-2 rounded">
                            <p class="text-sm font-medium text-center">Giao diện 2</p>
                            <p class="text-xs text-gray-500 text-center">Modern</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="reviews[template]" value="template3" {{ ($reviews['template'] ?? 'template1') == 'template3' ? 'checked' : '' }} class="sr-only peer">
                        <div class="border-2 rounded-lg p-4 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                            <img src="/assets/img/review-template-3.png" alt="Template 3" class="w-full mb-2 rounded">
                            <p class="text-sm font-medium text-center">Giao diện 3</p>
                            <p class="text-xs text-gray-500 text-center">Minimal</p>
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Fake Reviews -->
            <div class="border-t pt-4">
                <h3 class="font-semibold mb-3">Dữ liệu tự động đánh giá</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Loại dữ liệu mẫu</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="reviews[fake_type]" value="preset" {{ ($reviews['fake_type'] ?? 'preset') == 'preset' ? 'checked' : '' }} class="mr-2">
                                <span class="text-sm">Dữ liệu có sẵn</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="reviews[fake_type]" value="custom" {{ ($reviews['fake_type'] ?? 'preset') == 'custom' ? 'checked' : '' }} class="mr-2">
                                <span class="text-sm">Tự tạo</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="reviews[enable_fake]" value="1" {{ ($reviews['enable_fake'] ?? false) ? 'checked' : '' }} class="mr-2 rounded">
                            <span class="text-sm">Bật đánh giá tự động</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-6">Tự động thêm đánh giá mẫu cho sản phẩm mới</p>
                    </div>
                    <a href="{{ route('cms.reviews.fake') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                        Quản lý dữ liệu mẫu
                    </a>
                </div>
            </div>

            <!-- Images -->
            <div>
                <h3 class="font-semibold mb-3">Hình ảnh đánh giá</h3>
                <label class="flex items-center mb-3">
                    <input type="checkbox" name="reviews[allow_images]" value="1" {{ $allowImages ? 'checked' : '' }} class="mr-2 rounded">
                    <span class="text-sm">Cho phép upload hình ảnh</span>
                </label>
                <div>
                    <label class="block text-sm font-medium mb-2">Số ảnh tối đa</label>
                    <input type="number" name="reviews[max_images]" value="{{ $maxImages }}" min="1" max="10" class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>

            <!-- Rating Filter -->
            <div>
                <label class="block text-sm font-medium mb-2">Đánh giá tối thiểu</label>
                <select name="reviews[min_rating]" class="w-full px-4 py-2 border rounded-lg">
                    <option value="1" {{ $minRating == 1 ? 'selected' : '' }}>1 sao trở lên</option>
                    <option value="2" {{ $minRating == 2 ? 'selected' : '' }}>2 sao trở lên</option>
                    <option value="3" {{ $minRating == 3 ? 'selected' : '' }}>3 sao trở lên</option>
                    <option value="4" {{ $minRating == 4 ? 'selected' : '' }}>4 sao trở lên</option>
                    <option value="5" {{ $minRating == 5 ? 'selected' : '' }}>Chỉ 5 sao</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Chặn đánh giá dưới mức này (không khuyến khích)</p>
            </div>

            <!-- Rewards -->
            <div>
                <label class="block text-sm font-medium mb-2">Điểm thưởng</label>
                <input type="number" name="reviews[reward_points]" value="{{ $rewardPoints }}" min="0" class="w-full px-4 py-2 border rounded-lg">
                <p class="text-xs text-gray-500 mt-1">Số điểm thưởng khi khách hàng đánh giá (0 = không thưởng)</p>
            </div>

            <!-- Display Settings -->
            <div class="border-t pt-4">
                <h3 class="font-semibold mb-3">Hiển thị</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Sắp xếp mặc định</label>
                        <select name="reviews[default_sort]" class="w-full px-4 py-2 border rounded-lg">
                            <option value="newest" {{ ($reviews['default_sort'] ?? 'newest') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="highest" {{ ($reviews['default_sort'] ?? 'newest') == 'highest' ? 'selected' : '' }}>Rating cao nhất</option>
                            <option value="lowest" {{ ($reviews['default_sort'] ?? 'newest') == 'lowest' ? 'selected' : '' }}>Rating thấp nhất</option>
                            <option value="helpful" {{ ($reviews['default_sort'] ?? 'newest') == 'helpful' ? 'selected' : '' }}>Hữu ích nhất</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Số đánh giá/trang</label>
                        <input type="number" name="reviews[per_page]" value="{{ $reviews['per_page'] ?? 10 }}" min="5" max="50" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                </div>
            </div>

            <!-- Helpful Votes -->
            <div>
                <h3 class="font-semibold mb-3">Tương tác</h3>
                <label class="flex items-center">
                    <input type="checkbox" name="reviews[allow_helpful]" value="1" {{ ($reviews['allow_helpful'] ?? true) ? 'checked' : '' }} class="mr-2 rounded">
                    <span class="text-sm">Cho phép vote "Hữu ích"</span>
                </label>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lưu cấu hình</button>
        </div>
    </form>
</div>

<!-- Preview -->
<div class="bg-white rounded-lg shadow-sm p-6 mt-6">
    <h3 class="font-semibold mb-4">Preview đánh giá</h3>
    <div class="border rounded-lg p-4">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <span class="font-medium">Nguyễn Văn A</span>
                    @if($showVerified)
                    <span class="px-2 py-0.5 bg-green-100 text-green-800 text-xs rounded-full">✓ Đã mua hàng</span>
                    @endif
                </div>
                <div class="flex items-center gap-1 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                    <svg class="w-4 h-4 {{ $i <= 5 ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    @endfor
                    <span class="text-sm text-gray-500 ml-2">2 ngày trước</span>
                </div>
                <p class="text-sm text-gray-700 mb-3">Sản phẩm rất tốt, đóng gói cẩn thận, giao hàng nhanh. Mình rất hài lòng!</p>
                @if($allowImages)
                <div class="flex gap-2 mb-3">
                    <div class="w-20 h-20 bg-gray-200 rounded"></div>
                    <div class="w-20 h-20 bg-gray-200 rounded"></div>
                    <div class="w-20 h-20 bg-gray-200 rounded"></div>
                </div>
                @endif
                @if($reviews['allow_helpful'] ?? true)
                <div class="flex items-center gap-4 text-sm">
                    <button class="text-gray-600 hover:text-blue-600">👍 Hữu ích (12)</button>
                    <button class="text-gray-600 hover:text-gray-800">Trả lời</button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
    <p class="text-sm text-blue-800">
        <strong>Lưu ý:</strong> Đánh giá giúp tăng uy tín shop và conversion rate. 
        Khuyến khích khách hàng đánh giá bằng điểm thưởng hoặc voucher.
    </p>
</div>

@endsection
