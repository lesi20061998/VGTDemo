@php
    // Lấy các header styles từ database (bảng posts, post_type = 'header')
    $dbHeaders = \App\Models\Post::where('post_type', 'header')->get();
    
    $headerStyles = [];
    foreach ($dbHeaders as $header) {
        $headerStyles[$header->slug] = [
            'label' => $header->title,
            'image' => $header->featured_image ?? '/images/header/default.png',
            'desc'  => $header->excerpt
        ];
    }
    
    // Nếu chưa có trong DB, giữ lại một số mẫu mặc định
    if (empty($headerStyles)) {
        $headerStyles = [
            'style-1' => ['label' => 'Header Style 1', 'image' => '/images/header/header-style-1.png', 'desc' => 'Logo trái, menu giữa, icons phải'],
            'style-2' => ['label' => 'Header Style 2', 'image' => '/images/header/header-style-2.png', 'desc' => '2 hàng: Logo + icons / Menu'],
        ];
    }
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
