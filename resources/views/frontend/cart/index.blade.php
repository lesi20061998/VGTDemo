<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 py-8">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold text-white">Giỏ hàng</h1>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        @if(empty($cart))
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 mb-4">Giỏ hàng trống</p>
            <a href="/{{ request()->route('projectCode') }}/products" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">Tiếp tục mua sắm</a>
        </div>
        @else
        <div class="bg-white rounded-lg shadow">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">Sản phẩm</th>
                        <th class="px-6 py-3 text-center">Số lượng</th>
                        <th class="px-6 py-3 text-right">Giá</th>
                        <th class="px-6 py-3 text-right">Tổng</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart as $slug => $item)
                    <tr class="border-t">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-16 h-16 object-cover rounded">
                                <span class="font-semibold">{{ $item['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="/{{ request()->route('projectCode') }}/cart/update/{{ $slug }}" method="POST" class="inline">
                                @csrf
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="w-20 px-2 py-1 border rounded text-center" onchange="this.form.submit()">
                            </form>
                        </td>
                        <td class="px-6 py-4 text-right">{{ number_format($item['price']) }}đ</td>
                        <td class="px-6 py-4 text-right font-bold">{{ number_format($item['price'] * $item['quantity']) }}đ</td>
                        <td class="px-6 py-4 text-right">
                            <form action="/{{ request()->route('projectCode') }}/cart/remove/{{ $slug }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:text-red-800">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right font-bold text-lg">Tổng cộng:</td>
                        <td class="px-6 py-4 text-right font-bold text-2xl text-blue-600">{{ number_format($total) }}đ</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <div class="p-6 flex justify-between">
                <a href="/{{ request()->route('projectCode') }}/products" class="px-6 py-3 border rounded-lg hover:bg-gray-50">Tiếp tục mua sắm</a>
                <a href="/{{ request()->route('projectCode') }}/checkout" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Thanh toán</a>
            </div>
        </div>
        @endif
    </div>
</body>
</html>
