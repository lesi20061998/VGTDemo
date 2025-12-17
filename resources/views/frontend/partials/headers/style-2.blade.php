{{-- Header Style 2: 2 hàng - Logo + search + icons / Menu --}}
@php
    $showSearch = setting('show_search', true);
    $showCart = setting('show_cart', true);
    $showAccount = setting('show_account', false);
    $showWishlist = setting('show_wishlist', false);
@endphp

<header class="shadow-sm relative z-50" style="background-color: {{ $headerBg }}; color: {{ $headerText }};">
    {{-- Top Row: Logo + Search + Icons --}}
    <div class="border-b">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                {{-- Logo --}}
                <div class="flex-shrink-0">
                    @if($logo)
                        <a href="/{{ $projectCode }}"><img src="{{ $logo }}" alt="{{ $siteName }}" class="h-12"></a>
                    @else
                        <a href="/{{ $projectCode }}" class="text-2xl font-bold" style="color: {{ $headerText }};">{{ $siteName }}</a>
                    @endif
                </div>
                
                {{-- Search Bar --}}
                @if($showSearch)
                <div class="hidden md:flex flex-1 max-w-xl mx-8">
                    <form action="/{{ $projectCode }}/search" method="GET" class="relative w-full">
                        <input type="text" name="q" placeholder="Tìm kiếm sản phẩm..." class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-500 hover:text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                    </form>
                </div>
                @endif
                
                {{-- Icons --}}
                <div class="flex items-center gap-4">
                    {{-- Wishlist --}}
                    @if($showWishlist)
                    <a href="/{{ $projectCode }}/wishlist" class="hidden md:flex items-center gap-2 hover:text-blue-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        <span class="text-sm">Yêu thích</span>
                    </a>
                    @endif
                    
                    {{-- Account --}}
                    @if($showAccount)
                    <a href="/{{ $projectCode }}/account" class="hidden md:flex items-center gap-2 hover:text-blue-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="text-sm">Tài khoản</span>
                    </a>
                    @endif
                    
                    {{-- Cart --}}
                    @if($showCart)
                    <a href="/{{ $projectCode }}/cart" class="flex items-center gap-2 hover:text-blue-600 transition">
                        <div class="relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center cart-count">0</span>
                        </div>
                        <span class="hidden md:inline text-sm">Giỏ hàng</span>
                    </a>
                    @endif
                    
                    {{-- Mobile menu button --}}
                    <button type="button" class="lg:hidden p-2 hover:bg-gray-100 rounded" onclick="toggleMobileMenu()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Bottom Row: Navigation --}}
    @if($navMenu?->items)
    <div class="hidden lg:block bg-gray-50">
        <div class="container mx-auto px-4">
            @include('frontend.partials.navigation.desktop-menu', ['navMenu' => $navMenu, 'headerText' => $headerText])
        </div>
    </div>
    @endif
    
    {{-- Mobile Menu --}}
    <div id="mobile-menu" class="lg:hidden hidden border-t" style="background-color: {{ $headerBg }};">
        <div class="container mx-auto px-4 py-4">
            {{-- Mobile Search --}}
            @if($showSearch)
            <form action="/{{ $projectCode }}/search" method="GET" class="mb-4">
                <div class="relative">
                    <input type="text" name="q" placeholder="Tìm kiếm..." class="w-full px-4 py-2 border rounded-lg">
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
            </form>
            @endif
            
            @include('frontend.partials.navigation.mobile-menu', ['navMenu' => $navMenu, 'headerText' => $headerText])
        </div>
    </div>
</header>

<script>
function toggleMobileMenu() {
    document.getElementById('mobile-menu').classList.toggle('hidden');
}
</script>
