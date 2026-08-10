{{-- style-3-6 --}}
@php
    $showSearch = setting('show_search', true);
    $showCart = setting('show_cart', true);
    $showAccount = setting('show_account', false);
@endphp

<header class="shadow-sm relative z-50" style="background-color: {{ $headerBg }}; color: {{ $headerText }};">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4 absolute w-full top-0">            {{-- Logo --}}
            <div class="flex-shrink-0">
                @if($logo)
                    <a href="/{{ $projectCode }}"><img src="{{ $logo }}" alt="{{ $siteName }}" class="h-20 md:h-32 max-h-32 w-auto object-contain"></a>
                @else
                    <a href="/{{ $projectCode }}" class="text-2xl font-bold" style="color: {{ $headerText }};">{{ $siteName }}</a>
                @endif
            </div>            {{-- Navigation Menu --}}
            @if($navMenu?->items)
            <nav class="hidden lg:flex items-center gap-6">
                @foreach($navMenu->items as $item)
                    <a href="{{ $item->url }}" class="font-medium hover:text-blue-600 transition py-2" style="color: {{ $headerText }};">{{ $item->title }}</a>
                @endforeach
            </nav>
            @endif</div>
    </div>
</header>