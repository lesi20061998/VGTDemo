@extends('frontend.layouts.app')

@section('title', $title ?? 'Bài viết')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $title ?? 'Tin tức & Bài viết' }}</h1>
        @if(isset($description))
            <p class="text-gray-600 max-w-2xl mx-auto">{{ $description }}</p>
        @endif
    </div>

    {{-- Categories Navigation --}}
    @if(isset($categories) && $categories->count())
    <div class="flex flex-wrap justify-center gap-2 mb-8">
        <a href="{{ url()->current() }}" 
           class="px-4 py-2 rounded-full text-sm font-medium transition {{ !request('category') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Tất cả
        </a>
        @foreach($categories as $cat)
            <a href="{{ url()->current() }}?category={{ $cat->slug }}" 
               class="px-4 py-2 rounded-full text-sm font-medium transition {{ request('category') == $cat->slug ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>
    @endif

    {{-- Featured Post --}}
    @if(isset($featuredPost) && $featuredPost)
    <div class="mb-12">
        <article class="relative rounded-2xl overflow-hidden group">
            <div class="aspect-[21/9]">
                @if($featuredPost->thumbnail)
                    <img src="{{ $featuredPost->thumbnail }}" 
                         alt="{{ $featuredPost->title }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600"></div>
                @endif
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                <span class="inline-block px-3 py-1 bg-blue-600 rounded-full text-sm font-medium mb-4">Nổi bật</span>
                <h2 class="text-3xl font-bold mb-3 group-hover:text-blue-300 transition">
                    <a href="{{ route('frontend.page', $featuredPost->slug) }}">{{ $featuredPost->title }}</a>
                </h2>
                <p class="text-gray-200 mb-4 line-clamp-2 max-w-3xl">{{ $featuredPost->excerpt }}</p>
                <div class="flex items-center gap-4 text-sm text-gray-300">
                    <span>{{ $featuredPost->created_at->format('d/m/Y') }}</span>
                    @if($featuredPost->author)
                        <span>• {{ $featuredPost->author->name }}</span>
                    @endif
                </div>
            </div>
        </article>
    </div>
    @endif

    {{-- Posts Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($posts as $post)
            <article class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group">
                {{-- Thumbnail --}}
                <div class="relative aspect-video overflow-hidden">
                    @if($post->thumbnail)
                        <img src="{{ $post->thumbnail }}" 
                             alt="{{ $post->title }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                        </div>
                    @endif
                    
                    {{-- Category Badge --}}
                    @if($post->categories && $post->categories->first())
                        <span class="absolute top-3 left-3 px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded-full">
                            {{ $post->categories->first()->name }}
                        </span>
                    @endif
                </div>
                
                {{-- Content --}}
                <div class="p-6">
                    <div class="flex items-center gap-3 text-sm text-gray-500 mb-3">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $post->created_at->format('d/m/Y') }}
                        </span>
                        @if($post->reading_time)
                            <span>• {{ $post->reading_time }} phút đọc</span>
                        @endif
                    </div>
                    
                    <h3 class="font-bold text-lg text-gray-800 mb-3 line-clamp-2 group-hover:text-blue-600 transition">
                        <a href="{{ route('frontend.page', $post->slug) }}">{{ $post->title }}</a>
                    </h3>
                    
                    @if($post->excerpt)
                        <p class="text-gray-600 text-sm line-clamp-3 mb-4">{{ $post->excerpt }}</p>
                    @endif
                    
                    <a href="{{ route('frontend.page', $post->slug) }}" 
                       class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium text-sm">
                        Đọc tiếp
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </article>
        @empty
            <div class="col-span-full text-center py-16">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                <p class="text-gray-500 text-lg">Chưa có bài viết nào</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($posts->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $posts->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
