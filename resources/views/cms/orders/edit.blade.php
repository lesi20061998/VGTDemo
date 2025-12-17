{{-- MODIFIED: 2025-01-21 --}}
@extends('cms.layouts.app')

@section('title', 'Chỉnh sửa đơn hàng')
@section('page-title', 'Chỉnh sửa đơn hàng ' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
    <form method="POST" action="{{ isset($currentProject) && $currentProject ? route('project.admin.orders.update', [$currentProject->code, $order]) : route('cms.orders.update', $order) }}">
        @csrf @method('PUT')

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái đơn hàng</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Đã gửi</option>
                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Đã giao</option>
                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái thanh toán</label>
                <select name="payment_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Chưa thanh toán</option>
                    <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                    <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Thất bại</option>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú nội bộ</label>
            <textarea name="internal_notes" rows="4" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('internal_notes', $order->internal_notes) }}</textarea>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.orders.show', [$currentProject->code, $order]) : route('cms.orders.show', $order) }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                Hủy
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Cập nhật
            </button>
        </div>
    </form>
</div>
@endsection
