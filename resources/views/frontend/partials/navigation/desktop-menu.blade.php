{{-- Desktop Navigation Menu với Dropdown đa cấp --}}
@if($navMenu?->items)
<nav class="hidden lg:flex items-center gap-6">
    @foreach($navMenu->items as $item)
        @if($item->children && $item->children->count() > 0)
            {{-- Menu có submenu --}}
            <div class="relative group">
                <a href="{{ $item->url }}" class="font-medium hover:text-blue-600 transition flex items-center gap-1 py-2" style="color: {{ $headerText ?? '#000' }};">
                    {{ $item->title }}
                    <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </a>
                {{-- Dropdown submenu --}}
                <div class="absolute left-0 top-full pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                    <div class="bg-white shadow-lg rounded-lg border min-w-[200px] py-2">
                        @foreach($item->children as $child)
                            @if($child->children && $child->children->count() > 0)
                                {{-- Submenu cấp 2 --}}
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
            {{-- Menu không có submenu --}}
            <a href="{{ $item->url }}" class="font-medium hover:text-blue-600 transition py-2" style="color: {{ $headerText ?? '#000' }};">{{ $item->title }}</a>
        @endif
    @endforeach
</nav>
@endif
