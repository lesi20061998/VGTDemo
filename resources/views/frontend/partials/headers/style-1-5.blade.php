{{-- Header Style 1: Logo trái, menu giữa, icons phải --}}
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
            
            {{-- Navigation Menu với Dropdown --}}
            @if($navMenu?->items)
            <nav class="hidden lg:flex items-center gap-6">
                @foreach($navMenu->items as $item)
                    @if($item->children && $item->children->count() > 0)
                        <div class="relative group">
                            <a href="{{ $item->url }}" class="font-medium hover:text-blue-600 transition flex items-center gap-1 py-2" style="color: {{ $headerText }};">
                                {{ $item->title }}
                                <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </a>
                            <div class="absolute left-0 top-full pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                <div class="bg-white shadow-lg rounded-lg border min-w-[200px] py-2">
                                    @foreach($item->children as $child)
                                        @if($child->children && $child->children->count() > 0)
                                            <div class="relative group/sub">
                                                <a href="{{ $child->url }}" class="flex items-center justify-between px-4 py-2 text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                                                    {{ $child->title }}
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                </a>
                                                <div class="absolute left-full top-0 pl-2 opacity-0 invisible group-hover/sub:opacity-100 group-hover/sub:visible transition-all duration-200">
                                                    <div class="bg-white shadow-lg rounded-lg border min-w-[180px] py-2">
                                                        @foreach($child->children as $subChild)
                                                            <a href="{{ $subChild->url }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 hover:text-blue-600">{{ $subChild->title }}</a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <a href="{{ $child->url }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 hover:text-blue-600">{{ $child->title }}</a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ $item->url }}" class="font-medium hover:text-blue-600 transition py-2" style="color: {{ $headerText }};">{{ $item->title }}</a>
                    @endif
                @endforeach
            </nav>
            @endif
            
            {{-- Icons --}}
            <div class="flex items-center gap-3">
                {{-- Search --}}
                @if($showSearch)
                <button type="button" onclick="toggleSearchModal()" class="p-2 hover:bg-gray-100 rounded-full transition" title="Tìm kiếm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                @endif
                
                {{-- Account --}}
                @if($showAccount)
                <a href="/{{ $projectCode }}/account" class="p-2 hover:bg-gray-100 rounded-full transition" title="Tài khoản">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </a>
                @endif
                
                {{-- Cart --}}
                @if($showCart)
                <a href="/{{ $projectCode }}/cart" class="p-2 hover:bg-gray-100 rounded-full transition relative" title="Giỏ hàng">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center cart-count">0</span>
                </a>
                @endif
                
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
        </div>
    </div>
</header>

{{-- Search Modal --}}
@if($showSearch)
<div id="search-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="toggleSearchModal()"></div>
    <div class="absolute top-0 left-0 right-0 bg-white shadow-lg p-6">
        <div class="container mx-auto">
            <form action="/{{ $projectCode }}/search" method="GET" class="flex gap-4">
                <input type="text" name="q" placeholder="Nhập từ khóa tìm kiếm..." 
                       class="flex-1 px-4 py-3 border-2 rounded-lg focus:outline-none focus:border-blue-500 text-lg" autofocus>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Tìm kiếm
                </button>
                <button type="button" onclick="toggleSearchModal()" class="px-4 py-3 text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </form>
        </div>
    </div>
</div>
@endif

<script>
function toggleMobileMenu() {
    document.getElementById('mobile-menu').classList.toggle('hidden');
}

function toggleSearchModal() {
    const modal = document.getElementById('search-modal');
    if (modal) {
        modal.classList.toggle('hidden');
        if (!modal.classList.contains('hidden')) {
            modal.querySelector('input[name="q"]')?.focus();
        }
    }
}

// Close search modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('search-modal');
        if (modal && !modal.classList.contains('hidden')) {
            toggleSearchModal();
        }
    }
});
</script>
