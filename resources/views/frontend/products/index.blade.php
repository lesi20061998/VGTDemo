@extends('frontend.layouts.product-layout')

@section('page-title', 'Sản phẩm')

@section('product-content')
<div class="grid md:grid-cols-3 gap-6">
    @forelse($products ?? [] as $product)
    <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded-t-lg">
        <div class="p-4">
            <h3 class="font-bold mb-2">{{ $product->name }}</h3>
            <p class="text-gray-600 text-sm mb-2">{{ Str::limit($product->description, 80) }}</p>
            <div class="flex justify-between items-center">
                <span class="text-xl font-bold text-blue-600">{{ number_format($product->price) }}đ</span>
                <a href="/product/{{ $product->slug }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Xem</a>
            </div>
        </div>
    </div>
    @empty
    <p class="col-span-3 text-center text-gray-500">Chưa có sản phẩm</p>
    @endforelse
</div>
@endsection

@section('sidebar')
<div class="space-y-6">
    <div class="widget">
        <h3 class="font-bold mb-4">Danh mục</h3>
        <ul class="space-y-2">
            @foreach($categories ?? [] as $cat)
            <li><a href="/category/{{ $cat->slug }}" class="text-gray-700 hover:text-blue-600">{{ $cat->name }}</a></li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
