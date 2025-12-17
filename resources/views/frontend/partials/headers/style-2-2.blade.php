{{-- Header Style 2-2: 2 hàng - Logo giữa / Menu giữa --}}
@php
    $showSearch = setting('show_search', true);
    $showCart = setting('show_cart', true);
    $showAccount = setting('show_account', false);
@endphp

<header class="shadow-sm relative z-50" style="background-color: {{ $headerBg }}; color: {{ $headerText }};">
    {{-- Top Row: Logo Center --}}
    <div class="border-b py-6">
        <div class="container mx-auto px-4 text-center">
            @if($logo)
                <a href="/{{ $projectCode }}"><img src="{{ $logo }}" alt="{{ $siteName }}" class="h-14 mx-auto"></a>
            @else
                <a href="/{{ $projectCode }}" class="text-3xl font-bold" style="color: {{ $headerText }};">{{ $siteName }}</a>
            @endif
        </div>
    </div>
    
    {{-- Bottom Row: Navigation Center --}}
    @if($navMenu?->items)
    <div class="hidden lg:block">
        <div class="container mx-auto px-4 flex justify-center">
            @include('frontend.partials.navigation.desktop-menu', ['navMenu' => $navMenu, 'headerText' => $headerText])
        </div>
    </div>
    @endif
    
    {{-- Mobile: Icons --}}
    <div class="lg:hidden border-t">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <button type="button" class="p-2 hover:bg-gray-100 rounded" onclick="toggleMobileMenu()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="flex items-center gap-3">
                @if($showSearch)
                <button type="button" onclick="toggleSearchModal()" class="p-2 hover:bg-gray-100 rounded-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                @endif
                @if($showCart)
                <a href="/{{ $projectCode }}/cart" class="p-2 hover:bg-gray-100 rounded-full relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-4 h-4 rounded-full flex items-center justify-center cart-count">0</span>
                </a>
                @endif
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

{{-- Search Modal --}}
@if($showSearch)
@include('frontend.partials.search-modal', ['projectCode' => $projectCode])
@endif

<script>
function toggleMobileMenu() {
    document.getElementById('mobile-menu').classList.toggle('hidden');
}
</script>
