@extends('frontend.layouts.product-layout')

@php
    $projectCode = request()->route('projectCode');
@endphp

@section('page-title', $product->name ?? 'Chi tiết sản phẩm')

@section('product-content')
<!-- Breadcrumb -->
<nav class="text-sm mb-6">
    <ol class="flex items-center space-x-2">
        <li><a href="/{{ $projectCode }}" class="text-gray-500 hover:text-blue-600">Trang chủ</a></li>
        <li><span class="text-gray-400">/</span></li>
        <li><a href="/{{ $projectCode }}/san-pham" class="text-gray-500 hover:text-blue-600">Sản phẩm</a></li>
        @if($product->category)
        <li><span class="text-gray-400">/</span></li>
        <li><a href="/{{ $projectCode }}/danh-muc/{{ $product->category->slug }}" class="text-gray-500 hover:text-blue-600">{{ $product->category->name }}</a></li>
        @endif
        <li><span class="text-gray-400">/</span></li>
        <li class="text-gray-900 font-medium">{{ $product->name }}</li>
    </ol>
</nav>

<div class="bg-white rounded-lg shadow-sm p-6 mb-8">
    <div class="grid md:grid-cols-2 gap-8">
        <!-- Product Images -->
        <div class="product-gallery" x-data="{ mainImage: '{{ $product->featured_image ?? '/assets/img/placeholder-images-image_large.webp' }}' }">
            <div class="relative mb-4">
                <img :src="mainImage" alt="{{ $product->name }}" class="w-full rounded-lg shadow-sm">
                
                <!-- Badges -->
                <div class="absolute top-4 left-4 flex flex-col gap-2">
                    @if($product->is_featured)
                    <span class="bg-yellow-400 text-yellow-900 text-sm px-3 py-1 rounded-full">⭐ Nổi bật</span>
                    @endif
                    @if($product->is_bestseller)
                    <span class="bg-green-500 text-white text-sm px-3 py-1 rounded-full">📈 Bán chạy</span>
                    @endif
                    @if($product->sale_price && $product->sale_price < $product->price)
                    @php $discount = round((($product->price - $product->sale_price) / $product->price) * 100); @endphp
                    <span class="bg-red-500 text-white text-sm px-3 py-1 rounded-full">-{{ $discount }}%</span>
                    @endif
                </div>
            </div>
            
            @php
                $gallery = $product->gallery;
                if (is_string($gallery)) {
                    $gallery = json_decode($gallery, true) ?? [];
                }
            @endphp
            
            @if(!empty($gallery) && is_array($gallery))
            <div class="grid grid-cols-5 gap-2">
                <img src="{{ $product->featured_image }}" 
                     @click="mainImage = '{{ $product->featured_image }}'"
                     class="w-full h-20 object-cover rounded cursor-pointer border-2 hover:border-blue-500 transition"
                     :class="mainImage === '{{ $product->featured_image }}' ? 'border-blue-500' : 'border-transparent'">
                @foreach($gallery as $img)
                <img src="{{ $img }}" 
                     @click="mainImage = '{{ $img }}'"
                     class="w-full h-20 object-cover rounded cursor-pointer border-2 hover:border-blue-500 transition"
                     :class="mainImage === '{{ $img }}' ? 'border-blue-500' : 'border-transparent'">
                @endforeach
            </div>
            @endif
        </div>
        
        <!-- Product Info -->
        <div>
            <h1 class="text-2xl md:text-3xl font-bold mb-4">{{ $product->name }}</h1>
            
            <!-- SKU & Stock -->
            <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                @if($product->sku)
                <span>SKU: <strong>{{ $product->sku }}</strong></span>
                @endif
                <span class="{{ $product->stock_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $product->stock_quantity > 0 ? '✓ Còn hàng' : '✗ Hết hàng' }}
                </span>
                @if($product->views)
                <span>👁 {{ number_format($product->views) }} lượt xem</span>
                @endif
            </div>
            
            <!-- Price -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                @if($product->sale_price && $product->sale_price < $product->price)
                <div class="flex items-center gap-3">
                    <span class="text-3xl font-bold text-red-600">{{ number_format($product->sale_price) }}đ</span>
                    <span class="text-xl text-gray-400 line-through">{{ number_format($product->price) }}đ</span>
                    <span class="px-2 py-1 bg-red-100 text-red-600 text-sm font-medium rounded">Tiết kiệm {{ number_format($product->price - $product->sale_price) }}đ</span>
                </div>
                @else
                <span class="text-3xl font-bold text-blue-600">{{ number_format($product->price ?? 0) }}đ</span>
                @endif
            </div>
            
            <!-- Short Description -->
            @if($product->short_description)
            <div class="mb-6 text-gray-700 leading-relaxed">
                {!! $product->short_description !!}
            </div>
            @endif
            
            <!-- Add to Cart Form -->
            <form action="/{{ $projectCode }}/cart/add" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="id" value="{{ $product->id }}">
                <input type="hidden" name="slug" value="{{ $product->slug }}">
                <input type="hidden" name="name" value="{{ $product->name }}">
                <input type="hidden" name="sku" value="{{ $product->sku }}">
                <input type="hidden" name="price" value="{{ $product->sale_price ?? $product->price }}">
                <input type="hidden" name="image" value="{{ $product->featured_image }}">
                
                <div class="flex items-center gap-4">
                    <label class="font-medium">Số lượng:</label>
                    <div class="flex items-center border rounded-lg">
                        <button type="button" onclick="this.nextElementSibling.stepDown()" class="px-4 py-2 hover:bg-gray-100">-</button>
                        <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity ?? 99 }}" 
                               class="w-16 text-center border-x py-2 focus:outline-none">
                        <button type="button" onclick="this.previousElementSibling.stepUp()" class="px-4 py-2 hover:bg-gray-100">+</button>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-bold transition flex items-center justify-center gap-2"
                            {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                        🛒 Thêm vào giỏ hàng
                    </button>
                    <button type="submit" formaction="/{{ $projectCode }}/checkout" class="flex-1 bg-orange-500 text-white py-3 rounded-lg hover:bg-orange-600 font-bold transition">
                        ⚡ Mua ngay
                    </button>
                </div>
            </form>
            
            <!-- Contact -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-gray-700">
                    📞 Hotline: <a href="tel:{{ setting_string('hotline', '1900 1234') }}" class="font-bold text-blue-600">{{ setting_string('hotline', '1900 1234') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Product Tabs -->
<div class="bg-white rounded-lg shadow-sm" x-data="{ activeTab: 'description' }">
    <div class="border-b">
        <nav class="flex">
            <button @click="activeTab = 'description'" 
                    :class="activeTab === 'description' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
                    class="px-6 py-4 font-medium hover:text-blue-600 transition">
                Mô tả sản phẩm
            </button>
            <button @click="activeTab = 'specs'" 
                    :class="activeTab === 'specs' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
                    class="px-6 py-4 font-medium hover:text-blue-600 transition">
                Thông số kỹ thuật
            </button>
            <button @click="activeTab = 'reviews'" 
                    :class="activeTab === 'reviews' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
                    class="px-6 py-4 font-medium hover:text-blue-600 transition">
                Đánh giá ({{ $reviews->count() ?? 0 }})
            </button>
        </nav>
    </div>
    
    <div class="p-6">
        <!-- Description Tab -->
        <div x-show="activeTab === 'description'" class="prose max-w-none">
            {!! $product->description ?? '<p class="text-gray-500">Chưa có mô tả</p>' !!}
        </div>
        
        <!-- Specs Tab -->
        <div x-show="activeTab === 'specs'" x-cloak>
            @if($product->specifications)
            <div class="prose max-w-none">{!! $product->specifications !!}</div>
            @else
            <p class="text-gray-500">Chưa có thông số kỹ thuật</p>
            @endif
        </div>
        
        <!-- Reviews Tab -->
        <div x-show="activeTab === 'reviews'" x-cloak>
            @if(isset($reviews) && $reviews->count() > 0)
            <div class="space-y-4">
                @foreach($reviews as $review)
                <div class="border-b pb-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="font-bold">{{ $review->name }}</span>
                        <span class="text-yellow-500">{{ str_repeat('⭐', $review->rating) }}</span>
                        <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-gray-700">{{ $review->content }}</p>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500">Chưa có đánh giá nào</p>
            @endif
        </div>
    </div>
</div>

<!-- Related Products -->
@if(isset($relatedProducts) && $relatedProducts->count() > 0)
<div class="mt-8">
    <h2 class="text-2xl font-bold mb-6">Sản phẩm liên quan</h2>
    <div class="grid md:grid-cols-4 gap-6">
        @foreach($relatedProducts as $related)
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
            <a href="/{{ $projectCode }}/san-pham/{{ $related->slug }}">
                <img src="{{ $related->featured_image ?? '/assets/img/placeholder-images-image_large.webp' }}" 
                     alt="{{ $related->title }}" class="w-full h-40 object-cover">
            </a>
            <div class="p-4">
                <a href="/{{ $projectCode }}/san-pham/{{ $related->slug }}" class="font-bold hover:text-blue-600 line-clamp-2">{{ $related->title }}</a>
                <div class="mt-2">
                    @if(!empty($related->meta_data['sale_price']) && $related->meta_data['sale_price'] < ($related->meta_data['price'] ?? 0))
                    <span class="text-red-600 font-bold">{{ number_format($related->meta_data['sale_price']) }}đ</span>
                    <span class="text-gray-400 line-through text-sm ml-1">{{ number_format($related->meta_data['price'] ?? 0) }}đ</span>
                    @else
                    <span class="text-blue-600 font-bold">{{ number_format($related->meta_data['price'] ?? 0) }}đ</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection

@section('sidebar')
<div class="space-y-6">
    <!-- Categories -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <h3 class="font-bold mb-3 text-lg">Danh mục sản phẩm</h3>
        <ul class="space-y-2">
            @php
                $categories = \App\Models\Taxonomy::where('taxonomy', 'product_cat')->where('status', 'published')->orderBy('order')->get();
            @endphp
            @foreach($categories as $cat)
            <li>
                <a href="/{{ $projectCode }}/danh-muc/{{ $cat->slug }}" 
                   class="flex items-center py-2 px-3 rounded hover:bg-gray-50 {{ isset($product) && $product->taxonomies->contains('id', $cat->id) ? 'bg-blue-50 text-blue-600' : '' }}">
                    {{ $cat->name }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    
    <!-- Hot Products -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <h3 class="font-bold mb-3 text-lg">🔥 Sản phẩm hot</h3>
        <div class="space-y-3">
            @php
                $hotProducts = \App\Models\Post::where('post_type', 'product')
                    ->where('status', 'published')
                    ->where('meta_data->is_bestseller', true)
                    ->limit(5)
                    ->get();
            @endphp
            @foreach($hotProducts as $hot)
            <a href="/{{ $projectCode }}/san-pham/{{ $hot->slug }}" class="flex gap-3 hover:bg-gray-50 p-2 rounded transition">
                <img src="{{ $hot->featured_image ?? '/assets/img/placeholder-images-image_large.webp' }}" 
                     alt="{{ $hot->title }}" class="w-16 h-16 object-cover rounded">
                <div class="flex-1">
                    <h4 class="font-medium text-sm line-clamp-2">{{ $hot->title }}</h4>
                    <span class="text-blue-600 font-bold text-sm">{{ number_format($hot->meta_data['sale_price'] ?? ($hot->meta_data['price'] ?? 0)) }}đ</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endsection

<style>
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
[x-cloak] { display: none !important; }
</style>
