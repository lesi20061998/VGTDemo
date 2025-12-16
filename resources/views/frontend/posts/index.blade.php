@extends('frontend.layouts.post-layout')

@section('page-title', 'Tin tức')

@section('post-content')
<div class="space-y-6">
    @forelse($posts ?? [] as $post)
    <div class="bg-white rounded-lg shadow hover:shadow-lg transition flex">
        <img src="{{ $post->image }}" alt="{{ $post->title }}" class="w-64 h-48 object-cover rounded-l-lg">
        <div class="p-6 flex-1">
            <h3 class="text-2xl font-bold mb-2">{{ $post->title }}</h3>
            <p class="text-gray-600 mb-4">{{ Str::limit(strip_tags($post->content), 150) }}</p>
            <a href="/blog/{{ $post->slug }}" class="text-green-600 hover:text-green-700 font-semibold">Đọc thêm →</a>
        </div>
    </div>
    @empty
    <p class="text-center text-gray-500">Chưa có bài viết</p>
    @endforelse
</div>
@endsection

@section('sidebar')
<div class="space-y-6">
    <div class="widget">
        <h3 class="font-bold mb-4">Chủ đề</h3>
        <ul class="space-y-2">
            <li><a href="#" class="text-gray-700 hover:text-green-600">Công nghệ</a></li>
            <li><a href="#" class="text-gray-700 hover:text-green-600">Kinh doanh</a></li>
            <li><a href="#" class="text-gray-700 hover:text-green-600">Tin tức</a></li>
        </ul>
    </div>
</div>
@endsection
