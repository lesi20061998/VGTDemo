@extends('frontend.layouts.master')

@php
    $projectCode = request()->route('projectCode');
@endphp

@section('content')
<div class="bg-gradient-to-r from-blue-600 to-purple-600 py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-white">🛒 Giỏ hàng</h1>
        <nav class="text-white/80 text-sm mt-2">
            <a href="/{{ $projectCode }}" class="hover:text-white">Trang chủ</a> / 
            <span>Giỏ hàng</span>
        </nav>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center gap-2">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
        {{ session('success') }}
    </div>
    @endif

    @if(empty($cart))
    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
        <svg class="w-24 h-24 mx-auto text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <h2 class="text-2xl font-bold text-gray-700 mb-2">Giỏ hàng trống</h2>
        <p class="text-gray-500 mb-6">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
        <a href="/{{ $projectCode }}/san-pham" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 font-medium transition">
            🛍️ Tiếp tục mua sắm
        </a>
    </div>
    @else
    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-4 bg-gray-50 border-b">
                    <h2 class="font-bold text-lg">Sản phẩm trong giỏ ({{ count($cart) }})</h2>
                </div>
                
                <div class="divide-y">
                    @foreach($cart as $slug => $item)
                    <div class="p-4 flex items-center gap-4">
                        <img src="{{ $item['image'] ?? '/assets/img/placeholder-images-image_large.webp' }}" 
                             alt="{{ $item['name'] }}" 
                             class="w-20 h-20 object-cover rounded-lg">
                        
                        <div class="flex-1">
                            <h3 class="font-semibold">{{ $item['name'] }}</h3>
                            @if(isset($item['sku']))
                            <p class="text-sm text-gray-500">SKU: {{ $item['sku'] }}</p>
                            @endif
                            <p class="text-blue-600 font-bold mt-1">{{ number_format($item['price']) }}đ</p>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <form action="/{{ $projectCode }}/cart/update/{{ $slug }}" method="POST" class="flex items-center">
                                @csrf
                                <button type="button" onclick="this.nextElementSibling.stepDown(); this.form.submit();" 
                                        class="w-8 h-8 border rounded-l hover:bg-gray-100">-</button>
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" 
                                       class="w-12 h-8 border-t border-b text-center text-sm" 
                                       onchange="this.form.submit()">
                                <button type="button" onclick="this.previousElementSibling.stepUp(); this.form.submit();" 
                                        class="w-8 h-8 border rounded-r hover:bg-gray-100">+</button>
                            </form>
                        </div>
                        
                        <div class="text-right">
                            <p class="font-bold text-lg">{{ number_format($item['price'] * $item['quantity']) }}đ</p>
                            <form action="/{{ $projectCode }}/cart/remove/{{ $slug }}" method="POST" class="mt-1">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-500 hover:text-red-700 text-sm">🗑️ Xóa</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div class="mt-4">
                <a href="/{{ $projectCode }}/san-pham" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
                    ← Tiếp tục mua sắm
                </a>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                <h2 class="font-bold text-lg mb-4">Tóm tắt đơn hàng</h2>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tạm tính:</span>
                        <span>{{ number_format($total) }}đ</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Phí vận chuyển:</span>
                        <span class="text-green-600">Miễn phí</span>
                    </div>
                    <div class="border-t pt-3 flex justify-between text-lg font-bold">
                        <span>Tổng cộng:</span>
                        <span class="text-blue-600">{{ number_format($total) }}đ</span>
                    </div>
                </div>
                
                <a href="/{{ $projectCode }}/checkout" 
                   class="mt-6 block w-full bg-blue-600 text-white text-center py-3 rounded-lg hover:bg-blue-700 font-bold transition">
                    Tiến hành thanh toán →
                </a>
                
                <div class="mt-4 text-center text-sm text-gray-500">
                    <p>🔒 Thanh toán an toàn & bảo mật</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
