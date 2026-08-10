{{-- style-1-4 --}}
@php
    $showSearch = setting('show_search', true);
    $showCart = setting('show_cart', true);
    $showAccount = setting('show_account', false);
@endphp

<header class="shadow-sm relative z-50" style="background-color: {{ $headerBg }}; color: {{ $headerText }};">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4"><div class="flex items-center gap-6">            {{-- Logo --}}
            <div class="flex-shrink-0">
                @if($logo)
                    <a href="/{{ $projectCode }}"><img src="{{ $logo }}" alt="{{ $siteName }}" class="h-20 md:h-32 max-h-32 w-auto object-contain"></a>
                @else
                    <a href="/{{ $projectCode }}" class="text-2xl font-bold" style="color: {{ $headerText }};">{{ $siteName }}</a>
                @endif
            </div>            {{-- Hotline --}}
            <div class="hidden lg:flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <span class="font-bold text-blue-600">{{ $hotline }}</span>
            </div></div>            {{-- Navigation Menu --}}
            @if($navMenu?->items)
            <nav class="hidden lg:flex items-center gap-6">
                @foreach($navMenu->items as $item)
                    <a href="{{ $item->url }}" class="font-medium hover:text-blue-600 transition py-2" style="color: {{ $headerText }};">{{ $item->title }}</a>
                @endforeach
            </nav>
            @endif</div>
    </div>
</header>