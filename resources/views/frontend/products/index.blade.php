@extends('frontend.layouts.product-layout')

@php
    $projectCode = request()->route('projectCode');
    $pageTitle = isset($category) ? $category->name : 'Sản phẩm';
@endphp

@section('page-title', $pageTitle)

@section('product-content')
<!-- Filter & Sort Bar -->
<div class="bg-white rounded-lg shadow-sm p-4 mb-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <span class="text-gray-600">Sắp xếp:</span>
            <select onchange="window.location.href=this.value" class="border rounded-lg px-3 py-2 text-sm">
                <option value="?sort=newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                <option value="?sort=price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá thấp → cao</option>
                <option value="?sort=price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá cao → thấp</option>
                <option value="?sort=popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Phổ biến</option>
                <option value="?sort=name" {{ request('sort') == 'name' ? 'selected' : '' }}>Tên A-Z</option>
            </select>
        </div>
        <div class="text-sm text-gray-500">
            Hiển thị {{ $products->count() }} / {{ $products->total() }} sản phẩm
        </div>
    </div>
</div>

<!-- Products Grid -->
<div class="grid md:grid-cols-3 gap-6">
    @forelse($products ?? [] as $product)
    <div class="product-card bg-white rounded-lg shadow hover:shadow-xl transition overflow-hidden">
        <div class="relative">
            <a href="/{{ $projectCode }}/san-pham/{{ $product->slug }}">
                <img src="{{ $product->featured_image ?? '/assets/img/placeholder-images-image_large.webp' }}" 
                     alt="{{ $product->name }}" 
                     class="w-full h-48 object-cover">
            </a>
            
            <!-- Badges -->
            <div class="absolute top-2 left-2 flex flex-col gap-1">
                @if($product->is_featured)
                <span class="bg-yellow-400 text-yellow-900 text-xs px-2 py-1 rounded">⭐ Nổi bật</span>
                @endif
                @if($product->is_bestseller)
                <span class="bg-green-500 text-white text-xs px-2 py-1 rounded">📈 Bán chạy</span>
                @endif
                @if($product->is_favorite)
                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded">❤️ Yêu thích</span>
                @endif
            </div>
            
            @if($product->sale_price && $product->sale_price < $product->price)
            @php $discount = round((($product->price - $product->sale_price) / $product->price) * 100); @endphp
            <span class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded">-{{ $discount }}%</span>
            @endif
        </div>
        
        <div class="p-4">
            <a href="/{{ $projectCode }}/san-pham/{{ $product->slug }}" class="block">
                <h3 class="font-bold mb-2 line-clamp-2 hover:text-blue-600">{{ $product->name }}</h3>
            </a>
            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ Str::limit(strip_tags($product->short_description ?? $product->description), 80) }}</p>
            
            <div class="flex justify-between items-center">
                @if($product->sale_price && $product->sale_price < $product->price)
                <div>
                    <span class="text-lg font-bold text-red-600">{{ number_format($product->sale_price) }}đ</span>
                    <span class="text-sm text-gray-400 line-through block">{{ number_format($product->price) }}đ</span>
                </div>
                @else
                <span class="text-lg font-bold text-blue-600">{{ number_format($product->price ?? 0) }}đ</span>
                @endif
                
                <form action="/{{ $projectCode }}/cart/add" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <input type="hidden" name="name" value="{{ $product->name }}">
                    <input type="hidden" name="slug" value="{{ $product->slug }}">
                    <input type="hidden" name="price" value="{{ $product->sale_price ?? $product->price }}">
                    <input type="hidden" name="image" value="{{ $product->featured_image }}">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                        🛒 Thêm
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 text-center py-16">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>
        <p class="text-gray-500">Chưa có sản phẩm nào</p>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if(isset($products) && method_exists($products, 'links'))
<div class="mt-8">
    {{ $products->withQueryString()->links() }}
</div>
@endif
@endsection

@section('sidebar')
<div class="space-y-6">
    <!-- Search -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <h3 class="font-bold mb-3 text-lg">Tìm kiếm</h3>
        <form action="/{{ $projectCode }}/san-pham" method="GET">
            <div class="flex">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm sản phẩm..." 
                       class="flex-1 border rounded-l-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 text-white px-4 rounded-r-lg hover:bg-blue-700">
                    🔍
                </button>
            </div>
        </form>
    </div>

    <!-- Categories -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <h3 class="font-bold mb-3 text-lg">Danh mục</h3>
        <ul class="space-y-2">
            <li>
                <a href="/{{ $projectCode }}/san-pham" 
                   class="flex items-center justify-between py-2 px-3 rounded {{ !isset($category) ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50' }}">
                    <span>Tất cả sản phẩm</span>
                </a>
            </li>
            @foreach($categories ?? [] as $cat)
            <li>
                <a href="/{{ $projectCode }}/danh-muc/{{ $cat->slug }}" 
                   class="flex items-center justify-between py-2 px-3 rounded {{ isset($category) && $category->id == $cat->id ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50' }}">
                    <span>{{ $cat->name }}</span>
                    @if(isset($cat->products_count))
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $cat->products_count }}</span>
                    @endif
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    
    <!-- Price Filter -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <h3 class="font-bold mb-3 text-lg">Lọc theo giá</h3>
        <form action="/{{ $projectCode }}/san-pham" method="GET">
            <div class="space-y-3">
                <div>
                    <label class="text-sm text-gray-600">Từ:</label>
                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="0" 
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-sm text-gray-600">Đến:</label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="10,000,000" 
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <button type="submit" class="w-full bg-gray-800 text-white py-2 rounded hover:bg-gray-900">
                    Áp dụng
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

<style>
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.product-card { transition: all 0.3s ease; }
.product-card:hover { transform: translateY(-5px); }
</style>
