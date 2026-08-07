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
