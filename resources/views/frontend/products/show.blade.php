<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name ?? 'Chi tiết sản phẩm' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 py-8">
        <div class="container mx-auto px-4">
            <nav class="text-white text-sm mb-2">
                <a href="/" class="hover:underline">Trang chủ</a> / <a href="/products" class="hover:underline">Sản phẩm</a> / <span>{{ $product->name ?? '' }}</span>
            </nav>
            <h1 class="text-4xl font-bold text-white">{{ $product->name ?? '' }}</h1>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-8">
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <img src="{{ $product->image ?? '' }}" alt="{{ $product->name ?? '' }}" class="w-full rounded-lg">
                </div>
                <div>
                    <h2 class="text-3xl font-bold mb-4">{{ $product->name ?? '' }}</h2>
                    <div class="text-3xl font-bold text-blue-600 mb-6">{{ number_format($product->price ?? 0) }}đ</div>
                    <div class="mb-6">
                        <h3 class="font-bold mb-2">Mô tả:</h3>
                        <p class="text-gray-700">{!! $product->description ?? '' !!}</p>
                    </div>
                    <div class="mb-6">
                        <h3 class="font-bold mb-2">Thông số kỹ thuật:</h3>
                        <div class="text-gray-700">{!! $product->specifications ?? 'Đang cập nhật' !!}</div>
                    </div>
                    <form action="/{{ request()->route('projectCode') }}/cart/add" method="POST">
                        @csrf
                        <input type="hidden" name="slug" value="{{ $product->slug }}">
                        <input type="hidden" name="name" value="{{ $product->name }}">
                        <input type="hidden" name="price" value="{{ $product->price }}">
                        <input type="hidden" name="image" value="{{ $product->image }}">
                        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-bold">Thêm vào giỏ hàng</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
