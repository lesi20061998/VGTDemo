{{-- Topbar Style 5: Hai dòng - Contact trên, Menu dưới --}}
<div class="topbar hidden md:block" style="background-color: #ea580c; color: #ffffff;">
    <div class="container mx-auto px-4">
        <div class="py-2 flex justify-center items-center gap-6 text-sm border-b" style="border-color: rgba(255,255,255,0.2);">
            @if(setting('contact_phone'))
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
                    {{ setting('contact_phone') }}
                </span>
            @endif
            @if(setting('contact_email'))
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                    {{ setting('contact_email') }}
                </span>
            @endif
        </div>
        @if($topbarMenu && $topbarMenu->items)
            <nav class="py-2 flex justify-center gap-6 text-sm">
                @foreach($topbarMenu->items as $item)
                    <a href="{{ $item->url }}" class="hover:opacity-80" style="color: {{ $topbarText }};">{{ $item->title }}</a>
                @endforeach
            </nav>
        @endif
    </div>
</div>
