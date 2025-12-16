<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="bg-gradient-to-r from-green-600 to-teal-600 py-8">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold text-white">Thanh toán</h1>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <form action="/{{ request()->route('projectCode') }}/checkout/process" method="POST">
            @csrf
            <div class="grid md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h2 class="text-2xl font-bold mb-4">Thông tin giao hàng</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Họ tên *</label>
                                <input type="text" name="name" required class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Email *</label>
                                    <input type="email" name="email" required class="w-full px-4 py-2 border rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Số điện thoại *</label>
                                    <input type="tel" name="phone" required class="w-full px-4 py-2 border rounded-lg">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Địa chỉ *</label>
                                <x-location-select name="shipping" />
                                <textarea name="address_detail" rows="2" required class="w-full px-4 py-2 border rounded-lg mt-2" placeholder="Số nhà, tên đường..."></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Ghi chú</label>
                                <textarea name="note" rows="2" class="w-full px-4 py-2 border rounded-lg"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-2xl font-bold mb-4">Phương thức thanh toán</h2>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="cod" checked class="w-5 h-5">
                                <div>
                                    <div class="font-semibold">Thanh toán khi nhận hàng (COD)</div>
                                    <div class="text-sm text-gray-600">Thanh toán bằng tiền mặt khi nhận hàng</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="vnpay" class="w-5 h-5">
                                <div>
                                    <div class="font-semibold">VNPay</div>
                                    <div class="text-sm text-gray-600">Thanh toán qua VNPay</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="momo" class="w-5 h-5">
                                <div>
                                    <div class="font-semibold">Momo</div>
                                    <div class="text-sm text-gray-600">Thanh toán qua ví Momo</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                        <h2 class="text-2xl font-bold mb-4">Đơn hàng</h2>
                        <div class="space-y-3 mb-4">
                            @foreach($cart as $item)
                            <div class="flex justify-between text-sm">
                                <span>{{ $item['name'] }} x{{ $item['quantity'] }}</span>
                                <span class="font-semibold">{{ number_format($item['price'] * $item['quantity']) }}đ</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="border-t pt-4 mb-6">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Tổng cộng:</span>
                                <span class="text-blue-600">{{ number_format($total) }}đ</span>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 font-bold">Đặt hàng</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
