<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title ?? 'Chi tiết tin tức' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="bg-gradient-to-r from-green-600 to-teal-600 py-8">
        <div class="container mx-auto px-4">
            <nav class="text-white text-sm mb-2">
                <a href="/" class="hover:underline">Trang chủ</a> / <a href="/blog" class="hover:underline">Tin tức</a> / <span>{{ $post->title ?? '' }}</span>
            </nav>
            <h1 class="text-4xl font-bold text-white">{{ $post->title ?? '' }}</h1>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-8">
            <img src="{{ $post->image ?? '' }}" alt="{{ $post->title ?? '' }}" class="w-full h-96 object-cover rounded-lg mb-6">
            <div class="prose max-w-none">
                {!! $post->content ?? '' !!}
            </div>
        </div>
    </div>
</body>
</html>
