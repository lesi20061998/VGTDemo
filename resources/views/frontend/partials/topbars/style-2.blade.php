{{-- Topbar Style 2: Contact trái, Đăng ký/Đăng nhập phải --}}
<div class="topbar hidden md:block" style="background-color: #16a34a; color: #ffffff;">
    <div class="container mx-auto px-4 py-2.5 flex justify-between items-center text-sm">
        <div class="flex items-center gap-6">
            @if(setting('contact_phone'))
                <a href="tel:{{ setting('contact_phone') }}" class="flex items-center gap-2 hover:opacity-80">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
                    <span>{{ setting('contact_phone') }}</span>
                </a>
            @endif
            @if(setting('contact_email'))
                <a href="mailto:{{ setting('contact_email') }}" class="flex items-center gap-2 hover:opacity-80">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                    <span>{{ setting('contact_email') }}</span>
                </a>
            @endif
        </div>
        <div class="flex gap-2">
            <a href="#" class="px-3 py-1.5 rounded hover:opacity-90 transition-opacity flex items-center gap-1.5" style="background-color: rgba(255,255,255,0.2); color: #ffffff;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                <span>Đăng nhập</span>
            </a>
            <a href="#" class="px-3 py-1.5 rounded hover:opacity-90 transition-opacity flex items-center gap-1.5" style="background-color: #ffffff; color: #16a34a;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>Đăng ký</span>
            </a>
        </div>
    </div>
</div>
