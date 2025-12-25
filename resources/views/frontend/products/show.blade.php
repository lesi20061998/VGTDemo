@extends('frontend.layouts.product-layout')

@section('page-title', $product->name ?? 'Chi tiết sản phẩm')

@section('product-content')
<div class="grid md:grid-cols-2 gap-8">
    {{-- Product Images with Watermark --}}
    <div class="product-gallery">
        <x-watermark-image 
            :src="$product->featured_image ?? $product->image ?? ''" 
            :alt="$product->name ?? ''" 
            class="w-full rounded-lg shadow product-image-protected"
            img-class="w-full rounded-lg" />
        
        @if(!empty($product->gallery) && is_array($product->gallery))
        <div class="grid grid-cols-4 gap-2 mt-4">
            @foreach($product->gallery as $img)
            <x-watermark-image 
                :src="$img" 
                alt="Gallery image" 
                class="product-image-protected cursor-pointer hover:opacity-75"
                img-class="w-full h-20 object-cover rounded" />
            @endforeach
        </div>
        @endif
    </div>
    
    {{-- Product Info --}}
    <div>
        <h1 class="text-3xl font-bold mb-4">{{ $product->name ?? '' }}</h1>
        
        @if($product->sale_price && $product->sale_price < $product->price)
        <div class="mb-6">
            <span class="text-3xl font-bold text-red-600">{{ number_format($product->sale_price) }}đ</span>
            <span class="text-xl text-gray-400 line-through ml-2">{{ number_format($product->price) }}đ</span>
            @php
                $discount = round((($product->price - $product->sale_price) / $product->price) * 100);
            @endphp
            <span class="ml-2 px-2 py-1 bg-red-100 text-red-600 text-sm font-medium rounded">-{{ $discount }}%</span>
        </div>
        @else
        <div class="text-3xl font-bold text-blue-600 mb-6">{{ number_format($product->price ?? 0) }}đ</div>
        @endif
        
        @if($product->short_description)
        <div class="mb-6 text-gray-700">
            {!! $product->short_description !!}
        </div>
        @endif
        
        <div class="mb-6">
            <h3 class="font-bold mb-2">Mô tả:</h3>
            <div class="text-gray-700 prose max-w-none">{!! $product->description ?? '' !!}</div>
        </div>
        
        <form action="/{{ request()->route('projectCode') }}/cart/add" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="slug" value="{{ $product->slug }}">
            <input type="hidden" name="name" value="{{ $product->name }}">
            <input type="hidden" name="price" value="{{ $product->sale_price ?? $product->price }}">
            <input type="hidden" name="image" value="{{ $product->featured_image ?? $product->image }}">
            
            <div class="flex items-center gap-4">
                <label class="font-medium">Số lượng:</label>
                <input type="number" name="quantity" value="1" min="1" class="w-20 border rounded px-3 py-2 text-center">
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-bold transition">
                Thêm vào giỏ hàng
            </button>
        </form>
    </div>
</div>
@endsection

@section('sidebar')
<div class="space-y-6">
    <div class="widget">
        <h3 class="font-bold mb-4 text-lg">Danh mục sản phẩm</h3>
        <ul class="space-y-2">
            @foreach($categories ?? [] as $cat)
            <li><a href="/{{ request()->route('projectCode') }}/products?category={{ $cat->slug }}" class="text-gray-700 hover:text-blue-600">{{ $cat->name }}</a></li>
            @endforeach
        </ul>
    </div>
    
    @if(isset($relatedProducts) && count($relatedProducts) > 0)
    <div class="widget">
        <h3 class="font-bold mb-4 text-lg">Sản phẩm liên quan</h3>
        <div class="space-y-3">
            @foreach($relatedProducts as $related)
            <a href="/{{ request()->route('projectCode') }}/product/{{ $related->slug }}" class="flex gap-3 hover:bg-gray-100 p-2 rounded">
                <x-watermark-image 
                    :src="$related->featured_image ?? $related->image" 
                    :alt="$related->name" 
                    class="product-image-protected"
                    img-class="w-16 h-16 object-cover rounded" />
                <div>
                    <h4 class="font-medium text-sm">{{ $related->name }}</h4>
                    <span class="text-blue-600 font-bold text-sm">{{ number_format($related->price) }}đ</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
