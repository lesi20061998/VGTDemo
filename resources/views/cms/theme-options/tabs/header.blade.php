@php
    $headerStyles = [
        'style-1' => ['label' => 'Header Style 1', 'image' => '/images/header/header-style-1.png'],
        'style-1-1' => ['label' => 'Header Style 1-1', 'image' => '/images/header/header-style-1-1.png'],
        'style-1-2' => ['label' => 'Header Style 1-2', 'image' => '/images/header/header-style-1-2.png'],
        'style-1-3' => ['label' => 'Header Style 1-3', 'image' => '/images/header/header-style-1-3.png'],
        'style-1-4' => ['label' => 'Header Style 1-4', 'image' => '/images/header/header-style-1-4.png'],
        'style-1-5' => ['label' => 'Header Style 1-5', 'image' => '/images/header/header-style-1-5.png'],
        'style-1-6' => ['label' => 'Header Style 1-6', 'image' => '/images/header/header-style-1-6.png'],
        'style-1-7' => ['label' => 'Header Style 1-7', 'image' => '/images/header/header-style-1-7.png'],
        'style-2' => ['label' => 'Header Style 2', 'image' => '/images/header/header-style-2.png'],
        'style-2-1' => ['label' => 'Header Style 2-1', 'image' => '/images/header/header-style-2-1.png'],
        'style-2-2' => ['label' => 'Header Style 2-2', 'image' => '/images/header/header-style-2-2.png'],
        'style-3' => ['label' => 'Header Style 3', 'image' => '/images/header/header-style-3.png'],
        'style-3-2' => ['label' => 'Header Style 3-2', 'image' => '/images/header/header-style-3-2.png'],
        'style-3-3' => ['label' => 'Header Style 3-3', 'image' => '/images/header/header-style-3-3.png'],
        'style-3-4' => ['label' => 'Header Style 3-4', 'image' => '/images/header/header-style-3-4.png'],
        'style-3-5' => ['label' => 'Header Style 3-5', 'image' => '/images/header/header-style-3-5.png'],
        'style-3-6' => ['label' => 'Header Style 3-6', 'image' => '/images/header/header-style-3-6.png'],
        'style-3-7' => ['label' => 'Header Style 3-7', 'image' => '/images/header/header-style-3-7.png'],
        'style-4' => ['label' => 'Header Style 4', 'image' => '/images/header/header-style-4.png'],
        'style-4-1' => ['label' => 'Header Style 4-1', 'image' => '/images/header/header-style-4-1.png'],
        'style-4-2' => ['label' => 'Header Style 4-2', 'image' => '/images/header/header-style-4-2.png'],
        'style-4-3' => ['label' => 'Header Style 4-3', 'image' => '/images/header/header-style-4-3.png'],
        'style-4-4' => ['label' => 'Header Style 4-4', 'image' => '/images/header/header-style-4-4.png'],
        'style-4-5' => ['label' => 'Header Style 4-5', 'image' => '/images/header/header-style-4-5.png'],
        'style-4-6' => ['label' => 'Header Style 4-6', 'image' => '/images/header/header-style-4-6.png'],
    ];
@endphp

<div class="bg-white border rounded-lg p-6 shadow-sm">
    <div class="flex justify-between items-center mb-4 border-b pb-3">
        <h3 class="text-lg font-semibold text-gray-800">Header Styles</h3>
        <button onclick="downloadAllHeaders()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Tải tất cả về
        </button>
    </div>
    
    <div class="grid grid-cols-1 gap-4">
        @php $selectedHeader = $data['header_style'] ?? ''; @endphp
        @foreach($headerStyles as $key => $header)
        <label class="flex items-center gap-4 p-3 border-2 rounded-lg hover:border-blue-400 cursor-pointer relative {{ $selectedHeader == $key ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
            <input type="radio" name="header_style" value="{{ $key }}" 
                   {{ $selectedHeader == $key ? 'checked' : '' }} class="hidden">
            @if($selectedHeader == $key)
            <div class="absolute top-2 right-2 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold z-10">✓</div>
            @endif
            <div class="flex-shrink-0 w-3/4 h-20 bg-gray-50 rounded overflow-hidden">
                <img src="{{ asset($header['image']) }}" alt="{{ $header['label'] }}" class="w-full h-auto object-cover object-top">
            </div>
            <div class="flex-1 flex items-center justify-between gap-2">
                <h4 class="text-xs font-semibold text-gray-800">{{ $header['label'] }}</h4>
                <button type="button" onclick="event.stopPropagation(); downloadHeader('{{ asset($header['image']) }}', '{{ $key }}')" class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-all flex items-center gap-1 whitespace-nowrap">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Tải
                </button>
            </div>
        </label>
        @endforeach
    </div>
</div>

<script>
function downloadHeader(url, filename) {
    fetch(url)
        .then(response => response.blob())
        .then(blob => {
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename + '.png';
            link.click();
        });
}

function downloadAllHeaders() {
    const headers = @json(array_values($headerStyles));
    headers.forEach((header, index) => {
        setTimeout(() => {
            const filename = Object.keys(@json($headerStyles))[index];
            downloadHeader(header.image, filename);
        }, index * 500);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[type="radio"][name="header_style"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('input[name="header_style"]').forEach(r => {
                const label = r.closest('label');
                label.classList.remove('border-blue-500', 'bg-blue-50');
                label.classList.add('border-gray-200');
                const badge = label.querySelector('[data-badge]');
                if(badge) badge.remove();
            });
            
            const label = this.closest('label');
            label.classList.remove('border-gray-200');
            label.classList.add('border-blue-500', 'bg-blue-50');
            
            if(!label.querySelector('.bg-blue-600')) {
                const badge = document.createElement('div');
                badge.className = 'absolute top-2 right-2 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold z-10';
                badge.innerHTML = '✓';
                badge.setAttribute('data-badge', 'true');
                label.insertBefore(badge, label.firstChild);
            }
        });
    });
});
</script>
