{{-- Mobile Navigation Menu với Accordion --}}
@if($navMenu?->items)
<nav class="flex flex-col gap-1">
    @foreach($navMenu->items as $item)
        @if($item->children && $item->children->count() > 0)
            <div x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between py-3 font-medium hover:text-blue-600" style="color: {{ $headerText ?? '#000' }};">
                    {{ $item->title }}
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="pl-4 border-l-2 border-gray-200">
                    @foreach($item->children as $child)
                        @if($child->children && $child->children->count() > 0)
                            <div x-data="{ subOpen: false }">
                                <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between py-2 text-gray-600 hover:text-blue-600">
                                    {{ $child->title }}
                                    <svg :class="{'rotate-180': subOpen}" class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-collapse class="pl-4">
                                    @foreach($child->children as $subChild)
                                        <a href="{{ $subChild->url }}" class="block py-2 text-gray-500 hover:text-blue-600">{{ $subChild->title }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $child->url }}" class="block py-2 text-gray-600 hover:text-blue-600">{{ $child->title }}</a>
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            <a href="{{ $item->url }}" class="py-3 font-medium hover:text-blue-600" style="color: {{ $headerText ?? '#000' }};">{{ $item->title }}</a>
        @endif
    @endforeach
</nav>
@endif
