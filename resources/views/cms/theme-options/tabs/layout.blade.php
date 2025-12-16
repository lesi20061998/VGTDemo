@php
    $layouts = [
        'full-width' => [
            'label' => 'Full Width',
            'image' => '/images/layouts/layout-full.png',
            'description' => 'Nội dung full width không có sidebar'
        ],
        'full-width-banner' => [
            'label' => 'Full Width Banner',
            'image' => '/images/layouts/layout-full-banner.png',
            'description' => 'Full width với banner trên đầu'
        ],
        'sidebar-left' => [
            'label' => 'Sidebar Left',
            'image' => '/images/layouts/layout-sidebar-left.png',
            'description' => 'Sidebar bên trái, nội dung bên phải'
        ],
        'sidebar-left-1' => [
            'label' => 'Sidebar Left #1',
            'image' => '/images/layouts/layout-sidebar-left-banner-1.png',
            'description' => 'Sidebar trái với banner style 1'
        ],
        'sidebar-left-2' => [
            'label' => 'Sidebar Left #2',
            'image' => '/images/layouts/layout-sidebar-left-banner-2.png',
            'description' => 'Sidebar trái với banner style 2'
        ],
        'sidebar-right' => [
            'label' => 'Sidebar Right',
            'image' => '/images/layouts/layout-sidebar-right.png',
            'description' => 'Nội dung bên trái, sidebar bên phải'
        ],
        'sidebar-right-1' => [
            'label' => 'Sidebar Right #1',
            'image' => '/images/layouts/layout-sidebar-right-banner-1.png',
            'description' => 'Sidebar phải với banner style 1'
        ],
        'sidebar-right-2' => [
            'label' => 'Sidebar Right #2',
            'image' => '/images/layouts/layout-sidebar-right-banner-2.png',
            'description' => 'Sidebar phải với banner style 2'
        ]
    ];
@endphp

<div class="space-y-8">
    <!-- Page Layout -->
    <div>
        <h3 class="text-lg font-semibold mb-2">Page Layout</h3>
        <p class="text-sm text-gray-600 mb-4">Chọn layout cho trang chủ và các trang nội dung</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php $selectedPageLayout = $data['page_layout'] ?? 'full-width'; @endphp
            @foreach($layouts as $key => $layout)
            <label class="layout-option block p-3 border-2 rounded-lg hover:border-blue-400 cursor-pointer transition-all {{ $selectedPageLayout === $key ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-200' }}">
                <input type="radio" name="page_layout" value="{{ $key }}" 
                       {{ $selectedPageLayout === $key ? 'checked' : '' }} class="hidden layout-radio">
                <div class="relative">
                    @if($selectedPageLayout === $key)
                    <div class="absolute -top-1 -right-1 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold z-10">✓</div>
                    @endif
                    <img src="{{ asset($layout['image']) }}" alt="{{ $layout['label'] }}" class="w-full h-32 object-contain rounded mb-2 bg-white">
                </div>
                <span class="text-sm font-semibold text-center block mb-1">{{ $layout['label'] }}</span>
                <span class="text-xs text-gray-500 text-center block">{{ $layout['description'] }}</span>
            </label>
            @endforeach
        </div>

    </div>

    <!-- Post Layout -->
    <div>
        <h3 class="text-lg font-semibold mb-2">Post Layout</h3>
        <p class="text-sm text-gray-600 mb-4">Chọn layout cho trang danh sách bài viết và chi tiết bài viết</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php $selectedPostLayout = $data['post_layout'] ?? 'sidebar-right'; @endphp
            @foreach($layouts as $key => $layout)
            <label class="layout-option block p-3 border-2 rounded-lg hover:border-blue-400 cursor-pointer transition-all {{ $selectedPostLayout === $key ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-200' }}">
                <input type="radio" name="post_layout" value="{{ $key }}" 
                       {{ $selectedPostLayout === $key ? 'checked' : '' }} class="hidden layout-radio">
                <div class="relative">
                    @if($selectedPostLayout === $key)
                    <div class="absolute -top-1 -right-1 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold z-10">✓</div>
                    @endif
                    <img src="{{ asset($layout['image']) }}" alt="{{ $layout['label'] }}" class="w-full h-32 object-contain rounded mb-2 bg-white">
                </div>
                <span class="text-sm font-semibold text-center block mb-1">{{ $layout['label'] }}</span>
                <span class="text-xs text-gray-500 text-center block">{{ $layout['description'] }}</span>
            </label>
            @endforeach
        </div>

    </div>

    <!-- Products Category Layout -->
    <div>
        <h3 class="text-lg font-semibold mb-2">Products Category Layout</h3>
        <p class="text-sm text-gray-600 mb-4">Chọn layout cho trang danh mục sản phẩm và chi tiết sản phẩm</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php $selectedProductLayout = $data['product_layout'] ?? 'sidebar-left'; @endphp
            @foreach($layouts as $key => $layout)
            <label class="layout-option block p-3 border-2 rounded-lg hover:border-blue-400 cursor-pointer transition-all {{ $selectedProductLayout === $key ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-200' }}">
                <input type="radio" name="product_layout" value="{{ $key }}" 
                       {{ $selectedProductLayout === $key ? 'checked' : '' }} class="hidden layout-radio">
                <div class="relative">
                    @if($selectedProductLayout === $key)
                    <div class="absolute -top-1 -right-1 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold z-10">✓</div>
                    @endif
                    <img src="{{ asset($layout['image']) }}" alt="{{ $layout['label'] }}" class="w-full h-32 object-contain rounded mb-2 bg-white">
                </div>
                <span class="text-sm font-semibold text-center block mb-1">{{ $layout['label'] }}</span>
                <span class="text-xs text-gray-500 text-center block">{{ $layout['description'] }}</span>
            </label>
            @endforeach
        </div>

    </div>
</div>

<script>
const layouts = @json($layouts);

document.addEventListener('DOMContentLoaded', function() {
    // Handle layout selection
    document.querySelectorAll('.layout-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const name = this.name;
            const value = this.value;
            const layoutType = name.replace('_layout', '');
            
            // Update UI
            document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                const label = r.closest('.layout-option');
                label.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-200');
                label.classList.add('border-gray-200');
                const badge = label.querySelector('.absolute');
                if(badge && badge.textContent === '✓') badge.remove();
            });
            
            const label = this.closest('.layout-option');
            label.classList.remove('border-gray-200');
            label.classList.add('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-200');
            
            const badge = document.createElement('div');
            badge.className = 'absolute -top-1 -right-1 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold z-10';
            badge.innerHTML = '✓';
            label.querySelector('.relative').appendChild(badge);
            

        });
    });
});
</script>
