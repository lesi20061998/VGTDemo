{{-- Header Style 4: E-commerce - Logo + search + hotline + cart --}}
@php
    $showSearch = setting('show_search', true);
    $showCart = setting('show_cart', true);
    $showAccount = setting('show_account', false);
@endphp

<header class="shadow-sm relative z-50" style="background-color: {{ $headerBg }}; color: {{ $headerText }};">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between gap-4 py-4">
            {{-- Logo --}}
            <div class="flex-shrink-0">
                @if($logo)
                    <a href="/{{ $projectCode }}"><img src="{{ $logo }}" alt="{{ $siteName }}" class="h-10 md:h-12"></a>
                @else
                    <a href="/{{ $projectCode }}" class="text-xl md:text-2xl font-bold" style="color: {{ $headerText }};">{{ $siteName }}</a>
                @endif
            </div>
            
            {{-- Search Bar --}}
            @if($showSearch)
            <div class="hidden md:flex flex-1 max-w-2xl">
                <form action="/{{ $projectCode }}/search" method="GET" class="relative w-full flex">
                    <input type="text" name="q" placeholder="Tìm kiếm sản phẩm..." class="w-full px-4 py-3 border-2 border-r-0 rounded-l-lg focus:outline-none focus:border-blue-500">
                    <button type="submit" class="px-6 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </form>
            </div>
            @endif
            
            {{-- Right Icons --}}
            <div class="flex items-center gap-2 md:gap-6">
                {{-- Hotline --}}
                <a href="tel:{{ $hotline }}" class="hidden lg:flex items-center gap-2 hover:text-blue-600 transition">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <div>
                        <p class="text-xs text-gray-500">Hotline</p>
                        <p class="font-bold text-blue-600">{{ $hotline }}</p>
                    </div>
                </a>
                
                {{-- Account --}}
                @if($showAccount)
                <a href="/{{ $projectCode }}/account" class="hidden md:flex flex-col items-center hover:text-blue-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="text-xs mt-1">Tài khoản</span>
                </a>
                @endif
                
                {{-- Cart --}}
                @if($showCart)
                <a href="/{{ $projectCode }}/cart" class="flex flex-col items-center hover:text-blue-600 transition relative">
                    <div class="relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center cart-count">0</span>
                    </div>
                    <span class="text-xs mt-1 hidden md:inline">Giỏ hàng</span>
                </a>
                @endif
                
                {{-- Mobile menu button --}}
                <button type="button" class="lg:hidden p-2 hover:bg-gray-100 rounded" onclick="toggleMobileMenu()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
        
        {{-- Mobile Search --}}
        @if($showSearch)
        <div class="md:hidden pb-4">
            <form action="/{{ $projectCode }}/search" method="GET" class="relative flex">
                <input type="text" name="q" placeholder="Tìm kiếm..." class="w-full px-4 py-2 border rounded-l-lg focus:outline-none">
                <button type="submit" class="px-4 bg-blue-600 text-white rounded-r-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </form>
        </div>
        @endif
    </div>
    
    {{-- Navigation Bar --}}
    @if($navMenu?->items)
    <div class="hidden lg:block bg-blue-600 text-white">
        <div class="container mx-auto px-4">
            @include('frontend.partials.navigation.desktop-menu', ['navMenu' => $navMenu, 'headerText' => '#ffffff'])
        </div>
    </div>
    @endif
    
    {{-- Mobile Menu --}}
    <div id="mobile-menu" class="lg:hidden hidden border-t" style="background-color: {{ $headerBg }};">
        <div class="container mx-auto px-4 py-4">
            @include('frontend.partials.navigation.mobile-menu', ['navMenu' => $navMenu, 'headerText' => $headerText])
            <div class="mt-4 pt-4 border-t">
                <a href="tel:{{ $hotline }}" class="flex items-center gap-2 text-blue-600 hover:underline">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span class="font-bold">{{ $hotline }}</span>
                </a>
            </div>
        </div>
    </div>
</header>

<script>
function toggleMobileMenu() {
    document.getElementById('mobile-menu').classList.toggle('hidden');
}
</script>
