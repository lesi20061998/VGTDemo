{{-- Header Style 3: Sidebar menu trái (hamburger) --}}
@php
    $showSearch = setting('show_search', true);
    $showCart = setting('show_cart', true);
    $showAccount = setting('show_account', false);
@endphp

<header class="shadow-sm relative z-50" style="background-color: {{ $headerBg }}; color: {{ $headerText }};">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            {{-- Hamburger Menu --}}
            <button type="button" class="p-2 hover:bg-gray-100 rounded" onclick="toggleSidebar()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            
            {{-- Logo Center --}}
            <div class="flex-shrink-0">
                @if($logo)
                    <a href="/{{ $projectCode }}"><img src="{{ $logo }}" alt="{{ $siteName }}" class="h-10"></a>
                @else
                    <a href="/{{ $projectCode }}" class="text-xl font-bold" style="color: {{ $headerText }};">{{ $siteName }}</a>
                @endif
            </div>
            
            {{-- Right Icons --}}
            <div class="flex items-center gap-3">
                {{-- Search --}}
                @if($showSearch)
                <button type="button" onclick="toggleSearchModal()" class="p-2 hover:bg-gray-100 rounded-full" title="Tìm kiếm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                @endif
                
                {{-- Account --}}
                @if($showAccount)
                <a href="/{{ $projectCode }}/account" class="p-2 hover:bg-gray-100 rounded-full" title="Tài khoản">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </a>
                @endif
                
                {{-- Cart --}}
                @if($showCart)
                <a href="/{{ $projectCode }}/cart" class="p-2 hover:bg-gray-100 rounded-full relative" title="Giỏ hàng">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-4 h-4 rounded-full flex items-center justify-center cart-count">0</span>
                </a>
                @endif
            </div>
        </div>
    </div>
</header>

{{-- Sidebar Overlay --}}
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="toggleSidebar()"></div>

{{-- Sidebar Menu --}}
<div id="sidebar-menu" class="fixed top-0 left-0 w-72 h-full bg-white shadow-xl z-50 transform -translate-x-full transition-transform duration-300" style="background-color: {{ $headerBg }};">
    <div class="p-4 border-b flex justify-between items-center">
        @if($logo)
            <img src="{{ $logo }}" alt="{{ $siteName }}" class="h-8">
        @else
            <span class="text-xl font-bold" style="color: {{ $headerText }};">{{ $siteName }}</span>
        @endif
        <button type="button" onclick="toggleSidebar()" class="p-2 hover:bg-gray-100 rounded">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    
    {{-- Sidebar Search --}}
    @if($showSearch)
    <div class="p-4 border-b">
        <form action="/{{ $projectCode }}/search" method="GET">
            <div class="relative">
                <input type="text" name="q" placeholder="Tìm kiếm..." class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </div>
        </form>
    </div>
    @endif
    
    <div class="p-4 overflow-y-auto" style="max-height: calc(100vh - 200px);">
        @include('frontend.partials.navigation.mobile-menu', ['navMenu' => $navMenu, 'headerText' => $headerText])
    </div>
    
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t bg-gray-50">
        <a href="tel:{{ $hotline }}" class="flex items-center gap-2 text-blue-600 hover:underline">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            <span class="font-bold">{{ $hotline }}</span>
        </a>
    </div>
</div>

{{-- Search Modal --}}
@if($showSearch)
@include('frontend.partials.search-modal', ['projectCode' => $projectCode])
@endif

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar-menu');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}
</script>
