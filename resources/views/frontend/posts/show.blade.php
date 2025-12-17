@extends('frontend.layouts.post-layout')

@section('page-title', $post->title ?? 'Chi tiết bài viết')

@section('post-content')
<article>
    @if($post->featured_image ?? $post->image)
    <img src="{{ $post->featured_image ?? $post->image }}" alt="{{ $post->title ?? '' }}" class="w-full h-80 object-cover rounded-lg mb-6">
    @endif
    
    <header class="mb-6">
        <h1 class="text-3xl font-bold mb-3">{{ $post->title ?? '' }}</h1>
        <div class="flex items-center gap-4 text-gray-500 text-sm">
            @if($post->created_at)
            <span><i class="far fa-calendar mr-1"></i> {{ $post->created_at->format('d/m/Y') }}</span>
            @endif
            @if($post->author)
            <span><i class="far fa-user mr-1"></i> {{ $post->author->name ?? 'Admin' }}</span>
            @endif
            @if($post->views)
            <span><i class="far fa-eye mr-1"></i> {{ number_format($post->views) }} lượt xem</span>
            @endif
        </div>
    </header>
    
    <div class="prose max-w-none">
        {!! $post->content ?? '' !!}
    </div>
    
    @if($post->tags && count($post->tags) > 0)
    <div class="mt-8 pt-6 border-t">
        <span class="font-medium">Tags:</span>
        @foreach($post->tags as $tag)
        <a href="/{{ request()->route('projectCode') }}/blog?tag={{ $tag->slug }}" class="inline-block bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm hover:bg-gray-200 ml-2">{{ $tag->name }}</a>
        @endforeach
    </div>
    @endif
</article>

@if(isset($relatedPosts) && count($relatedPosts) > 0)
<div class="mt-10 pt-8 border-t">
    <h3 class="text-2xl font-bold mb-6">Bài viết liên quan</h3>
    <div class="grid md:grid-cols-3 gap-6">
        @foreach($relatedPosts as $related)
        <a href="/{{ request()->route('projectCode') }}/blog/{{ $related->slug }}" class="group">
            <img src="{{ $related->featured_image ?? $related->image }}" alt="{{ $related->title }}" class="w-full h-40 object-cover rounded-lg mb-3 group-hover:opacity-90 transition">
            <h4 class="font-semibold group-hover:text-green-600 transition">{{ $related->title }}</h4>
        </a>
        @endforeach
    </div>
</div>
@endif
@endsection

@section('sidebar')
<div class="space-y-6">
    <div class="widget">
        <h3 class="font-bold mb-4 text-lg">Chuyên mục</h3>
        <ul class="space-y-2">
            @foreach($categories ?? [] as $cat)
            <li><a href="/{{ request()->route('projectCode') }}/blog?category={{ $cat->slug }}" class="text-gray-700 hover:text-green-600 flex justify-between">
                <span>{{ $cat->name }}</span>
                <span class="text-gray-400">({{ $cat->posts_count ?? 0 }})</span>
            </a></li>
            @endforeach
        </ul>
    </div>
    
    @if(isset($recentPosts) && count($recentPosts) > 0)
    <div class="widget">
        <h3 class="font-bold mb-4 text-lg">Bài viết mới</h3>
        <div class="space-y-3">
            @foreach($recentPosts as $recent)
            <a href="/{{ request()->route('projectCode') }}/blog/{{ $recent->slug }}" class="flex gap-3 hover:bg-gray-100 p-2 rounded">
                <img src="{{ $recent->featured_image ?? $recent->image }}" alt="{{ $recent->title }}" class="w-16 h-16 object-cover rounded">
                <div>
                    <h4 class="font-medium text-sm line-clamp-2">{{ $recent->title }}</h4>
                    <span class="text-gray-400 text-xs">{{ $recent->created_at->format('d/m/Y') }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
