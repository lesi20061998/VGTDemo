<?php
// MODIFIED: 2025-01-21

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Requests\OrderRequest;
use App\Traits\HasAlerts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
    use HasAlerts;
    public function index(Request $request)
    {
        $orders = Order::with(['items'])
            ->when($request->search, fn($q) => $q->search($request->search))
            ->filter($request->only(['status', 'payment_status', 'date_from', 'date_to']))
            ->latest()
            ->paginate(config('app.admin_per_page', 20));

        return view('cms.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'statusHistories.user']);
        return view('cms.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load('items.product');
        return view('cms.orders.edit', compact('order'));
    }

    public function update(OrderRequest $request, Order $order)
    {
        $validated = $request->validated();
        $order->update($validated);

        return redirect()->route('cms.orders.show', $order)->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật đơn hàng thành công!'
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'notes' => 'nullable|string|max:500'
        ]);

        $order->updateStatus(
            $validated['status'], 
            $validated['notes'] ?? null, 
            auth()->id()
        );

        // Clear cache
        Cache::forget('order_reports_*');

        return redirect()->back()->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật trạng thái đơn hàng thành công!'
        ]);
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $order->update([
            'payment_status' => $validated['payment_status'],
            'paid_at' => $validated['payment_status'] === 'paid' ? now() : null,
        ]);

        Cache::forget('order_reports_*');

        return redirect()->back()->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật trạng thái thanh toán thành công!'
        ]);
    }

    public function addNote(Request $request, Order $order)
    {
        $validated = $request->validate([
            'internal_notes' => 'required|string|max:1000'
        ]);

        $order->update([
            'internal_notes' => $order->internal_notes . "\n\n" . 
                              "[" . now()->format('d/m/Y H:i') . " - " . auth()->user()->name . "]\n" . 
                              $validated['internal_notes']
        ]);

        return redirect()->back()->with('alert', [
            'type' => 'success',
            'message' => 'Thêm ghi chú thành công!'
        ]);
    }

    public function destroy(Order $order)
    {
        $order->delete();

        Cache::forget('order_reports_*');

        return redirect()->route('cms.orders.index')->with('alert', [
            'type' => 'success',
            'message' => 'Xóa đơn hàng thành công!'
        ]);
    }

    // ===== REPORTS =====
    public function reports(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->endOfMonth()->toDateString());

        $cacheKey = 'order_reports_' . md5($dateFrom . $dateTo);

        $reportData = Cache::remember($cacheKey, 60, function () use ($dateFrom, $dateTo) {
            return [
                'total_sales' => Order::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->where('payment_status', 'paid')
                    ->sum('total_amount'),
                'total_orders' => Order::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
                'orders_by_status' => Order::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status'),
                'daily_revenue' => Order::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->where('payment_status', 'paid')
                    ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),
                'top_products' => OrderItem::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->selectRaw('product_name, SUM(quantity) as total_quantity, SUM(total_price) as total_revenue, COUNT(DISTINCT order_id) as order_count')
                    ->groupBy('product_name')
                    ->orderByDesc('total_revenue')
                    ->limit(10)
                    ->get(),
            ];
        });

        return view('cms.orders.reports', array_merge($reportData, [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]));
    }
}
