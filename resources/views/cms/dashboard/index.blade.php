{{-- MODIFIED: 2025-01-21 --}}
@extends('cms.layouts.app')

@section('title', 'Dashboard Analytics')
@section('page-title', 'Dashboard Analytics')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium">Doanh thu hôm nay</p>
                <p class="text-3xl font-bold">{{ number_format($today_revenue) }}đ</p>
                <p class="text-blue-100 text-xs mt-1">+12% so với hôm qua</p>
            </div>
            <div class="p-3 bg-blue-400 bg-opacity-30 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium">Đơn hàng hôm nay</p>
                <p class="text-3xl font-bold">{{ $today_orders }}</p>
                <p class="text-green-100 text-xs mt-1">+8% so với hôm qua</p>
            </div>
            <div class="p-3 bg-green-400 bg-opacity-30 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium">Tổng người dùng</p>
                <p class="text-3xl font-bold">{{ number_format($total_users) }}</p>
                <p class="text-purple-100 text-xs mt-1">+{{ $new_users_today }} hôm nay</p>
            </div>
            <div class="p-3 bg-purple-400 bg-opacity-30 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm font-medium">Đơn chờ xử lý</p>
                <p class="text-3xl font-bold">{{ $pending_orders }}</p>
                <p class="text-orange-100 text-xs mt-1">Cần xử lý ngay</p>
            </div>
            <div class="p-3 bg-orange-400 bg-opacity-30 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Revenue Chart -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900">Doanh thu 7 ngày qua</h3>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                <span class="text-sm text-gray-600">Doanh thu</span>
            </div>
        </div>
        <canvas id="revenueChart" width="400" height="200"></canvas>
    </div>

    <!-- Orders Chart -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900">Đơn hàng 7 ngày qua</h3>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                <span class="text-sm text-gray-600">Đơn hàng</span>
            </div>
        </div>
        <canvas id="ordersChart" width="400" height="200"></canvas>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Device Analytics -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-6">Thiết bị truy cập</h3>
        <div class="relative">
            <canvas id="deviceChart" width="200" height="200"></canvas>
        </div>
        <div class="mt-4 space-y-2">
            @foreach($device_chart as $device)
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $device['color'] }}"></div>
                    <span class="text-sm text-gray-600">{{ $device['device'] }}</span>
                </div>
                <span class="text-sm font-medium text-gray-900">{{ $device['percentage'] }}%</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Traffic Sources -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-6">Nguồn truy cập</h3>
        <div class="space-y-4">
            @foreach($traffic_chart as $traffic)
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-900">{{ $traffic['source'] }}</span>
                        <span class="text-sm text-gray-600">{{ number_format($traffic['visitors']) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $traffic['percentage'] }}%"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-6">Sản phẩm bán chạy</h3>
        <div class="space-y-4">
            @foreach($top_products as $index => $product)
            <div class="flex items-center">
                <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                    <span class="text-sm font-medium text-gray-600">{{ $index + 1 }}</span>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                    <p class="text-xs text-gray-500">{{ $product->total_sold }} đã bán</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@include('cms.dashboard.visitor-stats')

<!-- Recent Orders -->
<div class="bg-white rounded-xl shadow-lg p-6">
    <h3 class="text-lg font-bold text-gray-900 mb-6">Đơn hàng gần đây</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã đơn</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng tiền</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($recent_orders as $order)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $order->order_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->customer_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($order->total_amount) }}đ</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->status_badge }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($revenue_chart->pluck('date')) !!},
        datasets: [{
            label: 'Doanh thu',
            data: {!! json_encode($revenue_chart->pluck('revenue')) !!},
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { display: false } },
            x: { grid: { display: false } }
        }
    }
});

// Orders Chart
const ordersCtx = document.getElementById('ordersChart').getContext('2d');
new Chart(ordersCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($orders_chart->pluck('date')) !!},
        datasets: [{
            label: 'Đơn hàng',
            data: {!! json_encode($orders_chart->pluck('orders')) !!},
            backgroundColor: '#10B981',
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { display: false } },
            x: { grid: { display: false } }
        }
    }
});

// Device Chart
const deviceCtx = document.getElementById('deviceChart').getContext('2d');
new Chart(deviceCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($device_chart->pluck('device')) !!},
        datasets: [{
            data: {!! json_encode($device_chart->pluck('percentage')) !!},
            backgroundColor: {!! json_encode($device_chart->pluck('color')) !!},
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        cutout: '70%'
    }
});
</script>
@endsection
