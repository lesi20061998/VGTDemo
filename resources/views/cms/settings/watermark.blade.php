@extends('cms.layouts.app')

@section('title', 'Bảo vệ bản quyền')
@section('page-title', 'Watermark - Đóng dấu ảnh')

@section('content')
<div class="mb-6">
    <a href="{{ route('cms.settings.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Quay lại</a>
</div>

<div class="bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('cms.settings.save') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="space-y-6">
            @php
                $watermark = setting('watermark', []);
                $enabled = $watermark['enabled'] ?? false;
                $image = $watermark['image'] ?? '';
                $position = $watermark['position'] ?? 'bottom-right';
                $offsetX = $watermark['offset_x'] ?? 10;
                $offsetY = $watermark['offset_y'] ?? 10;
                $scale = $watermark['scale'] ?? 20;
                $opacity = $watermark['opacity'] ?? 80;
            @endphp

            <!-- Enable Watermark -->
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="watermark[enabled]" value="1" {{ $enabled ? 'checked' : '' }} class="mr-2">
                    <span class="font-medium">Bật đóng dấu ảnh tự động</span>
                </label>
            </div>

            <!-- Upload Watermark -->
            <div x-data="{ watermarkImage: '{{ $image }}' }">
                <label class="block text-sm font-medium mb-2">Ảnh watermark</label>
                <div class="mb-2">
                    <img :src="watermarkImage || '{{ asset('assets/img/placeholder-images-image_large.webp') }}'" 
                         id="watermarkImagePreview" class="max-w-xs border rounded">
                </div>
                <input type="hidden" name="watermark[image]" x-model="watermarkImage">
                @include('cms.components.media-manager')
                <p class="text-xs text-gray-500 mt-1">Nên dùng ảnh PNG có nền trong suốt</p>
            </div>

            <!-- Alignment -->
            <div>
                <label class="block text-sm font-medium mb-2">Vị trí (Alignment)</label>
                <select name="watermark[position]" class="w-full px-4 py-2 border rounded-lg">
                    <option value="top-left" {{ $position == 'top-left' ? 'selected' : '' }}>Trên - Trái</option>
                    <option value="top-center" {{ $position == 'top-center' ? 'selected' : '' }}>Trên - Giữa</option>
                    <option value="top-right" {{ $position == 'top-right' ? 'selected' : '' }}>Trên - Phải</option>
                    <option value="center-left" {{ $position == 'center-left' ? 'selected' : '' }}>Giữa - Trái</option>
                    <option value="center" {{ $position == 'center' ? 'selected' : '' }}>Chính giữa</option>
                    <option value="center-right" {{ $position == 'center-right' ? 'selected' : '' }}>Giữa - Phải</option>
                    <option value="bottom-left" {{ $position == 'bottom-left' ? 'selected' : '' }}>Dưới - Trái</option>
                    <option value="bottom-center" {{ $position == 'bottom-center' ? 'selected' : '' }}>Dưới - Giữa</option>
                    <option value="bottom-right" {{ $position == 'bottom-right' ? 'selected' : '' }}>Dưới - Phải</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Offset X -->
                <div>
                    <label class="block text-sm font-medium mb-2">Offset X (px)</label>
                    <input type="number" name="watermark[offset_x]" value="{{ $offsetX }}" class="w-full px-4 py-2 border rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Khoảng cách theo chiều ngang</p>
                </div>

                <!-- Offset Y -->
                <div>
                    <label class="block text-sm font-medium mb-2">Offset Y (px)</label>
                    <input type="number" name="watermark[offset_y]" value="{{ $offsetY }}" class="w-full px-4 py-2 border rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Khoảng cách theo chiều dọc</p>
                </div>
            </div>

            <!-- Scale -->
            <div>
                <label class="block text-sm font-medium mb-2">Tỷ lệ (Scale) - {{ $scale }}%</label>
                <input type="range" name="watermark[scale]" min="5" max="100" value="{{ $scale }}" class="w-full" oninput="this.previousElementSibling.textContent = 'Tỷ lệ (Scale) - ' + this.value + '%'">
                <p class="text-xs text-gray-500 mt-1">Kích thước watermark so với ảnh gốc</p>
            </div>

            <!-- Opacity -->
            <div>
                <label class="block text-sm font-medium mb-2">Độ mờ (Opacity) - {{ $opacity }}%</label>
                <input type="range" name="watermark[opacity]" min="10" max="100" value="{{ $opacity }}" class="w-full" oninput="this.previousElementSibling.textContent = 'Độ mờ (Opacity) - ' + this.value + '%'">
                <p class="text-xs text-gray-500 mt-1">Độ trong suốt của watermark</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lưu cấu hình</button>
        </div>
    </form>
</div>

<!-- Preview Demo -->
<div class="bg-gray-50 rounded-lg p-6 mt-6">
    <h3 class="text-lg font-semibold mb-4">Xem trước (Demo)</h3>
    <div class="relative inline-block border-4 border-dashed border-gray-300 rounded">
        <img src="{{ asset('assets/img/placeholder-images-image_large.webp') }}" alt="Demo" class="w-full max-w-2xl" id="demoImage">
        <img src="{{ $image ? asset($image) : 'https://via.placeholder.com/150x50/3b82f6/ffffff?text=LOGO' }}" alt="Watermark" id="watermarkPreview" class="absolute" style="opacity: {{ $opacity / 100 }}; width: {{ $scale }}%;">
    </div>
    <p class="text-xs text-gray-500 mt-2">Đây là hình minh họa, thay đổi cài đặt và lưu lại để xem kết quả</p>
</div>

<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
    <p class="text-sm text-blue-800">
        <strong>Lưu ý:</strong> Watermark sẽ tự động được áp dụng cho tất cả ảnh mới upload. 
        Ảnh cũ cần xử lý lại thủ công nếu muốn thêm watermark.
    </p>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function updatePreview() {
    const position = document.querySelector('[name="watermark[position]"]').value;
    const offsetX = document.querySelector('[name="watermark[offset_x]"]').value;
    const offsetY = document.querySelector('[name="watermark[offset_y]"]').value;
    const scale = document.querySelector('[name="watermark[scale]"]').value;
    const opacity = document.querySelector('[name="watermark[opacity]"]').value;
    const watermark = document.getElementById('watermarkPreview');
    
    watermark.style.width = scale + '%';
    watermark.style.opacity = opacity / 100;
    
    const positions = {
        'top-left': `top: ${offsetY}px; left: ${offsetX}px;`,
        'top-center': `top: ${offsetY}px; left: 50%; transform: translateX(-50%);`,
        'top-right': `top: ${offsetY}px; right: ${offsetX}px;`,
        'center-left': `top: 50%; left: ${offsetX}px; transform: translateY(-50%);`,
        'center': `top: 50%; left: 50%; transform: translate(-50%, -50%);`,
        'center-right': `top: 50%; right: ${offsetX}px; transform: translateY(-50%);`,
        'bottom-left': `bottom: ${offsetY}px; left: ${offsetX}px;`,
        'bottom-center': `bottom: ${offsetY}px; left: 50%; transform: translateX(-50%);`,
        'bottom-right': `bottom: ${offsetY}px; right: ${offsetX}px;`
    };
    
    watermark.style.cssText = positions[position] + `opacity: ${opacity / 100}; width: ${scale}%;`;
}

document.querySelectorAll('[name="watermark[position]"], [name="watermark[offset_x]"], [name="watermark[offset_y]"], [name="watermark[scale]"], [name="watermark[opacity]"]').forEach(el => {
    el.addEventListener('change', updatePreview);
    el.addEventListener('input', updatePreview);
});

// Listen for media selection
window.addEventListener('media-selected', function(e) {
    if (e.detail.items.length > 0) {
        const selected = e.detail.items[0];
        const watermarkData = Alpine.$data(document.querySelector('[x-data*="watermarkImage"]'));
        if (watermarkData) {
            watermarkData.watermarkImage = selected.url;
        }
        document.getElementById('watermarkPreview').src = selected.url;
    }
});

updatePreview();
</script>

@include('cms.components.media-manager')
@endsection
