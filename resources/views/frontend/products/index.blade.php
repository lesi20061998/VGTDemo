@extends('frontend.layouts.product-layout')

@section('page-title', 'Sản phẩm')

@section('product-content')
<div class="grid md:grid-cols-3 gap-6">
    @forelse($products ?? [] as $product)
    <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
        <x-watermark-image 
            :src="$product->featured_image ?? $product->image" 
            :alt="$product->name" 
            class="product-image-protected w-full"
            img-class="w-full h-48 object-cover rounded-t-lg" />
        <div class="p-4">
            <h3 class="font-bold mb-2">{{ $product->name }}</h3>
            <p class="text-gray-600 text-sm mb-2">{{ Str::limit($product->short_description ?? $product->description, 80) }}</p>
            <div class="flex justify-between items-center">
                @if($product->sale_price && $product->sale_price < $product->price)
                <div>
                    <span class="text-lg font-bold text-red-600">{{ number_format($product->sale_price) }}đ</span>
                    <span class="text-sm text-gray-400 line-through ml-1">{{ number_format($product->price) }}đ</span>
                </div>
                @else
                <span class="text-xl font-bold text-blue-600">{{ number_format($product->price) }}đ</span>
                @endif
                <a href="/{{ request()->route('projectCode') }}/product/{{ $product->slug }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Xem</a>
            </div>
        </div>
    </div>
    @empty
    <p class="col-span-3 text-center text-gray-500 py-12">Chưa có sản phẩm</p>
    @endforelse
</div>

@if(isset($products) && $products instanceof \Illuminate\Pagination\LengthAwarePaginator)
<div class="mt-8">
    {{ $products->links() }}
</div>
@endif
@endsection

@section('sidebar')
<div class="space-y-6">
    <div class="widget">
        <h3 class="font-bold mb-4 text-lg">Danh mục</h3>
        <ul class="space-y-2">
            @foreach($categories ?? [] as $cat)
            <li>
                <a href="/{{ request()->route('projectCode') }}/products?category={{ $cat->slug }}" 
                   class="text-gray-700 hover:text-blue-600 flex items-center justify-between">
                    <span>{{ $cat->name }}</span>
                    @if(isset($cat->products_count))
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $cat->products_count }}</span>
                    @endif
                </a>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
