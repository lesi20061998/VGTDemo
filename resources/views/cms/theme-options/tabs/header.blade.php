@php
    $headerStyles = [
        'style-1' => ['label' => 'Header Style 1', 'image' => '/images/header/header-style-1.png', 'desc' => 'Logo trái, menu giữa, icons phải'],
        'style-1-1' => ['label' => 'Header Style 1-1', 'image' => '/images/header/header-style-1-1.png', 'desc' => 'Logo trái, menu + search phải'],
        'style-1-2' => ['label' => 'Header Style 1-2', 'image' => '/images/header/header-style-1-2.png', 'desc' => 'Logo trái, menu giữa, hotline phải'],
        'style-1-3' => ['label' => 'Header Style 1-3', 'image' => '/images/header/header-style-1-3.png', 'desc' => 'Logo trái, search giữa, menu phải'],
        'style-1-4' => ['label' => 'Header Style 1-4', 'image' => '/images/header/header-style-1-4.png', 'desc' => 'Logo + hotline trái, menu phải'],
        'style-1-5' => ['label' => 'Header Style 1-5', 'image' => '/images/header/header-style-1-5.png', 'desc' => 'Logo trái, menu + cart phải'],
        'style-1-6' => ['label' => 'Header Style 1-6', 'image' => '/images/header/header-style-1-6.png', 'desc' => 'Logo giữa, menu 2 bên'],
        'style-1-7' => ['label' => 'Header Style 1-7', 'image' => '/images/header/header-style-1-7.png', 'desc' => 'Logo trái, menu + user phải'],
        'style-2' => ['label' => 'Header Style 2', 'image' => '/images/header/header-style-2.png', 'desc' => '2 hàng: Logo + icons / Menu'],
        'style-2-1' => ['label' => 'Header Style 2-1', 'image' => '/images/header/header-style-2-1.png', 'desc' => '2 hàng: Logo + search / Menu'],
        'style-2-2' => ['label' => 'Header Style 2-2', 'image' => '/images/header/header-style-2-2.png', 'desc' => '2 hàng: Logo giữa / Menu giữa'],
        'style-3' => ['label' => 'Header Style 3', 'image' => '/images/header/header-style-3.png', 'desc' => 'Sidebar menu trái'],
        'style-3-2' => ['label' => 'Header Style 3-2', 'image' => '/images/header/header-style-3-2.png', 'desc' => 'Sidebar menu phải'],
        'style-3-3' => ['label' => 'Header Style 3-3', 'image' => '/images/header/header-style-3-3.png', 'desc' => 'Hamburger menu fullscreen'],
        'style-3-4' => ['label' => 'Header Style 3-4', 'image' => '/images/header/header-style-3-4.png', 'desc' => 'Hamburger + logo giữa'],
        'style-3-5' => ['label' => 'Header Style 3-5', 'image' => '/images/header/header-style-3-5.png', 'desc' => 'Minimal với hamburger'],
        'style-3-6' => ['label' => 'Header Style 3-6', 'image' => '/images/header/header-style-3-6.png', 'desc' => 'Transparent overlay'],
        'style-3-7' => ['label' => 'Header Style 3-7', 'image' => '/images/header/header-style-3-7.png', 'desc' => 'Fixed sidebar'],
        'style-4' => ['label' => 'Header Style 4', 'image' => '/images/header/header-style-4.png', 'desc' => 'E-commerce: Logo + search + cart'],
        'style-4-1' => ['label' => 'Header Style 4-1', 'image' => '/images/header/header-style-4-1.png', 'desc' => 'E-commerce: Categories dropdown'],
        'style-4-2' => ['label' => 'Header Style 4-2', 'image' => '/images/header/header-style-4-2.png', 'desc' => 'E-commerce: Mega menu'],
        'style-4-3' => ['label' => 'Header Style 4-3', 'image' => '/images/header/header-style-4-3.png', 'desc' => 'E-commerce: 3 hàng full'],
        'style-4-4' => ['label' => 'Header Style 4-4', 'image' => '/images/header/header-style-4-4.png', 'desc' => 'E-commerce: Compact'],
        'style-4-5' => ['label' => 'Header Style 4-5', 'image' => '/images/header/header-style-4-5.png', 'desc' => 'E-commerce: Dark theme'],
        'style-4-6' => ['label' => 'Header Style 4-6', 'image' => '/images/header/header-style-4-6.png', 'desc' => 'E-commerce: Sticky cart'],
    ];
@endphp

<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Chọn Header Style</h3>
            <p class="text-sm text-gray-500">Chọn kiểu header phù hợp với website của bạn</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @php $selectedHeader = $data['header_style'] ?? 'style-1'; @endphp
        @foreach($headerStyles as $key => $header)
        <label class="header-option block p-4 border-2 rounded-lg hover:border-blue-400 cursor-pointer transition-all relative {{ $selectedHeader === $key ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-200' }}">
            <input type="radio" name="header_style" value="{{ $key }}" 
                   {{ $selectedHeader === $key ? 'checked' : '' }} class="hidden header-radio">
            @if($selectedHeader === $key)
            <div class="absolute top-2 right-2 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold z-10">✓</div>
            @endif
            <div class="bg-gray-100 rounded overflow-hidden mb-3">
                <img src="{{ asset($header['image']) }}" alt="{{ $header['label'] }}" class="w-full h-24 object-cover object-top" onerror="this.src='https://via.placeholder.com/400x100?text={{ urlencode($header['label']) }}'">
            </div>
            <h4 class="font-semibold text-gray-800 text-sm">{{ $header['label'] }}</h4>
            <p class="text-xs text-gray-500">{{ $header['desc'] }}</p>
        </label>
        @endforeach
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.header-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.header-option').forEach(opt => {
                opt.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-200');
                opt.classList.add('border-gray-200');
                const badge = opt.querySelector('.bg-blue-600');
                if(badge) badge.remove();
            });
            
            const label = this.closest('.header-option');
            label.classList.remove('border-gray-200');
            label.classList.add('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-200');
            
            const badge = document.createElement('div');
            badge.className = 'absolute top-2 right-2 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold z-10';
            badge.innerHTML = '✓';
            label.appendChild(badge);
        });
    });
});
</script>
