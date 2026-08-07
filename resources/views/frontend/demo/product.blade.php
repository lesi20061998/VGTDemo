@extends('frontend.layouts.product-layout')

@section('page-title', 'Demo Chi tiết sản phẩm')

@section('product-content')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <div class="product-gallery">
            <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800" alt="Product Image" class="w-full h-96 object-cover rounded-lg shadow-md border">
        </div>
        <div class="product-info flex flex-col justify-center">
            <h2 class="text-3xl font-bold mb-2 text-gray-800">Tai nghe Over-ear Bluetooth Cao cấp</h2>
            <div class="text-2xl font-bold text-red-600 mb-4">2,500,000 đ</div>
            
            <p class="text-gray-600 mb-6 border-b pb-6">
                Sản phẩm tai nghe mẫu với chất lượng âm thanh tuyệt đỉnh. Đây là trang mô phỏng Layout Sản phẩm. Bạn hãy đổi Layout trong CMS (VD: Sidebar Left/Right) để kiểm tra.
            </p>
            
            <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg w-full md:w-auto transition">
                THÊM VÀO GIỎ HÀNG
            </button>
        </div>
    </div>
    
    <div class="border-t pt-8">
        <h3 class="text-xl font-bold mb-4">Mô tả sản phẩm</h3>
        <p class="text-gray-700 leading-relaxed">
            Nội dung chi tiết của sản phẩm sẽ được hiển thị ở đây. Hệ thống sử dụng Tailwind CSS để cấu trúc layout, giúp giao diện hiển thị chuyên nghiệp, gọn gàng và tương thích hoàn hảo trên các thiết bị di động.
        </p>
    </div>
@endsection

@section('sidebar')
    <div class="mb-8">
        <h3 class="font-bold mb-4 text-lg border-b pb-2">Danh mục sản phẩm</h3>
        <ul class="space-y-3 text-gray-600">
            <li>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" class="rounded text-blue-500 border-gray-300">
                    <span class="hover:text-blue-500">Tai nghe (24)</span>
                </label>
            </li>
            <li>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" class="rounded text-blue-500 border-gray-300">
                    <span class="hover:text-blue-500">Loa Bluetooth (12)</span>
                </label>
            </li>
            <li>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" class="rounded text-blue-500 border-gray-300">
                    <span class="hover:text-blue-500">Phụ kiện (35)</span>
                </label>
            </li>
        </ul>
    </div>
    
    <div class="mb-8">
        <h3 class="font-bold mb-4 text-lg border-b pb-2">Lọc theo giá</h3>
        <div class="space-y-3 text-gray-600">
            <input type="range" class="w-full" min="0" max="10000000">
            <div class="flex justify-between text-sm">
                <span>0 đ</span>
                <span>10,000,000 đ</span>
            </div>
        </div>
    </div>
@endsection
