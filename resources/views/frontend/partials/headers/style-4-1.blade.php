{{-- style-4-1 --}}
@php
    $showSearch = setting('show_search', true);
    $showCart = setting('show_cart', true);
    $showAccount = setting('show_account', false);
@endphp

<header class="shadow-sm relative z-50" style="background-color: {{ $headerBg }}; color: {{ $headerText }};">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">            {{-- Logo --}}
            <div class="flex-shrink-0">
                @if($logo)
                    <a href="/{{ $projectCode }}"><img src="{{ $logo }}" alt="{{ $siteName }}" class="h-20 md:h-32 max-h-32 w-auto object-contain"></a>
                @else
                    <a href="/{{ $projectCode }}" class="text-2xl font-bold" style="color: {{ $headerText }};">{{ $siteName }}</a>
                @endif
            </div><div class="flex-1 max-w-2xl mx-8 flex"><button class="bg-gray-100 px-4 rounded-l border">Danh mục</button><input class="flex-1 border px-4 py-2" placeholder="Tìm kiếm..."><button class="bg-blue-600 text-white px-4 rounded-r">Tìm</button></div>            {{-- Icons --}}
            <div class="flex items-center gap-3">
                @if($showSearch)
                <button type="button" onclick="toggleSearchModal()" class="p-2 hover:bg-gray-100 rounded-full transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></button>
                @endif
                @if($showCart)
                <a href="/{{ $projectCode }}/cart" class="p-2 hover:bg-gray-100 rounded-full transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg></a>
                @endif
                <button type="button" class="lg:hidden p-2 hover:bg-gray-100 rounded" onclick="toggleMobileMenu()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
            </div></div><div class="bg-blue-600 text-white py-2 flex justify-center">            {{-- Navigation Menu --}}
            @if($navMenu?->items)
            <nav class="hidden lg:flex items-center gap-6">
                @foreach($navMenu->items as $item)
                    <a href="{{ $item->url }}" class="font-medium hover:text-blue-600 transition py-2" style="color: {{ $headerText }};">{{ $item->title }}</a>
                @endforeach
            </nav>
            @endif</div>
    </div>
</header>