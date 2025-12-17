{{-- Header Style 1-2: Logo trái, menu giữa, hotline + icons phải --}}
@php
    $showSearch = setting('show_search', true);
    $showCart = setting('show_cart', true);
    $showAccount = setting('show_account', false);
@endphp

<header class="shadow-sm relative z-50" style="background-color: {{ $headerBg }}; color: {{ $headerText }};">
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
            
            {{-- Navigation Menu --}}
            @include('frontend.partials.navigation.desktop-menu', ['navMenu' => $navMenu, 'headerText' => $headerText])
            
            {{-- Right Section: Icons + Hotline --}}
            <div class="flex items-center gap-4">
                {{-- Search --}}
                @if($showSearch)
                <button type="button" onclick="toggleSearchModal()" class="hidden md:block p-2 hover:bg-gray-100 rounded-full transition" title="Tìm kiếm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                @endif
                
                {{-- Cart --}}
                @if($showCart)
                <a href="/{{ $projectCode }}/cart" class="hidden md:block p-2 hover:bg-gray-100 rounded-full transition relative" title="Giỏ hàng">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center cart-count">0</span>
                </a>
                @endif
                
                {{-- Hotline --}}
                <div class="hidden md:flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Hotline</p>
                        <a href="tel:{{ $hotline }}" class="font-bold text-blue-600 hover:underline">{{ $hotline }}</a>
                    </div>
                </div>
                
                {{-- Mobile menu button --}}
                <button type="button" class="lg:hidden p-2 hover:bg-gray-100 rounded" onclick="toggleMobileMenu()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </div>
    
    {{-- Mobile Menu --}}
    <div id="mobile-menu" class="lg:hidden hidden border-t" style="background-color: {{ $headerBg }};">
        <div class="container mx-auto px-4 py-4">
            {{-- Mobile Search --}}
            @if($showSearch)
            <form action="/{{ $projectCode }}/search" method="GET" class="mb-4">
                <div class="relative">
                    <input type="text" name="q" placeholder="Tìm kiếm..." class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
            </form>
            @endif
            
            @include('frontend.partials.navigation.mobile-menu', ['navMenu' => $navMenu, 'headerText' => $headerText])
            
            <div class="mt-4 pt-4 border-t flex items-center justify-between">
                <a href="tel:{{ $hotline }}" class="flex items-center gap-2 text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span class="font-bold">{{ $hotline }}</span>
                </a>
                @if($showCart)
                <a href="/{{ $projectCode }}/cart" class="flex items-center gap-2 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Giỏ hàng</span>
                </a>
                @endif
            </div>
        </div>
    </div>
</header>

{{-- Search Modal --}}
@if($showSearch)
@include('frontend.partials.search-modal', ['projectCode' => $projectCode])
@endif

<script>
function toggleMobileMenu() {
    document.getElementById('mobile-menu').classList.toggle('hidden');
}
</script>
