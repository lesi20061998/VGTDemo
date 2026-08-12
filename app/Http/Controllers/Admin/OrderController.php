<?php

// MODIFIED: 2025-01-21

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Traits\HasAlerts;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
    use HasAlerts;

    public function index($projectCode, Request $request)
    {
        try {
            $orders = Order::with(['items'])
                ->when($request->search, fn ($q) => $q->search($request->search))
                ->filter($request->only(['status', 'payment_status', 'date_from', 'date_to']))
                ->latest()
                ->paginate(config('app.admin_per_page', 20));
        } catch (\Exception $e) {
            $orders = new LengthAwarePaginator([], 0, config('app.admin_per_page', 20), 1, ['path' => $request->url()]);
        }

        return view('cms.orders.index', compact('orders'));
    }

    public function create($projectCode)
    {
        return view('cms.orders.create');
    }

    public function store(Request $request, $projectCode)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|array',
            'billing_address' => 'nullable|array',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
            'total_amount' => 'nullable|numeric'
        ]);
        
        if (empty($validated['billing_address']['address']) && empty($validated['billing_address']['city'])) {
            $validated['billing_address'] = $validated['shipping_address'];
        }

        $validated['order_number'] = 'ORD-' . strtoupper(uniqid());
        $validated['status'] = 'pending';
        $validated['payment_status'] = 'pending';
        $validated['total_amount'] = $request->input('total_amount', 0);
        $validated['project_id'] = session('current_project_id') ?? 1;
        
        $order = Order::create($validated);

        return redirect()->route('project.admin.orders.edit', [$projectCode, $order])->with('alert', [
            'type' => 'success',
            'message' => 'Tạo đơn hàng thành công! Bạn có thể thêm chi tiết đơn hàng.',
        ]);
    }

    public function show($projectCode, Order $order)
    {
        $order->load(['items.product', 'statusHistories.user']);

        return view('cms.orders.show', compact('order'));
    }

    public function edit($projectCode, Order $order)
    {
        $order->load('items.product');

        return view('cms.orders.edit', compact('order'));
    }

    public function update(OrderRequest $request, $projectCode, Order $order)
    {
        $validated = $request->validated();
        $order->update($validated);

        return redirect()->route('project.admin.orders.show', [$projectCode, $order])->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật đơn hàng thành công!',
        ]);
    }

    public function updateStatus(Request $request, $projectCode, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'notes' => 'nullable|string|max:500',
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
            'message' => 'Cập nhật trạng thái đơn hàng thành công!',
        ]);
    }

    public function updatePaymentStatus(Request $request, $projectCode, Order $order)
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
            'message' => 'Cập nhật trạng thái thanh toán thành công!',
        ]);
    }

    public function addNote(Request $request, $projectCode, Order $order)
    {
        $validated = $request->validate([
            'internal_notes' => 'required|string|max:1000',
        ]);

        $order->update([
            'internal_notes' => $order->internal_notes."\n\n".
                              '['.now()->format('d/m/Y H:i').' - '.auth()->user()->name."]\n".
                              $validated['internal_notes'],
        ]);

        return redirect()->back()->with('alert', [
            'type' => 'success',
            'message' => 'Thêm ghi chú thành công!',
        ]);
    }

    public function printInvoice($projectCode, Order $order)
    {
        $order->load('items.product');
        return view('cms.orders.print', compact('order'));
    }

    public function destroy($projectCode, Order $order)
    {
        $order->delete();

        Cache::forget('order_reports_*');

        $route = request()->route('projectCode')
            ? route('project.admin.orders.index', request()->route('projectCode'))
            : route('cms.orders.index');

        return redirect($route)->with('alert', [
            'type' => 'success',
            'message' => 'Xóa đơn hàng thành công!',
        ]);
    }

    // ===== REPORTS =====
    public function reports($projectCode, Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->endOfMonth()->toDateString());

        $cacheKey = 'order_reports_'.md5($dateFrom.$dateTo);

        $reportData = [];
        try {
            $reportData = Cache::remember($cacheKey, 60, function () use ($dateFrom, $dateTo) {
                return [
                    'total_sales' => Order::withoutGlobalScope('project')
                        ->whereBetween('created_at', [$dateFrom, $dateTo])
                        ->where('payment_status', 'paid')
                        ->sum('total_amount'),
                    'total_orders' => Order::withoutGlobalScope('project')
                        ->whereBetween('created_at', [$dateFrom, $dateTo])->count(),
                    'orders_by_status' => Order::withoutGlobalScope('project')
                        ->whereBetween('created_at', [$dateFrom, $dateTo])
                        ->selectRaw('status, COUNT(*) as count')
                        ->groupBy('status')
                        ->pluck('count', 'status'),
                    'daily_revenue' => Order::withoutGlobalScope('project')
                        ->whereBetween('created_at', [$dateFrom, $dateTo])
                        ->where('payment_status', 'paid')
                        ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as count')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get(),
                    'top_products' => OrderItem::withoutGlobalScope('project')
                        ->whereBetween('created_at', [$dateFrom, $dateTo])
                        ->selectRaw('product_name, SUM(quantity) as total_quantity, SUM(total_price) as total_revenue, COUNT(DISTINCT order_id) as order_count')
                        ->groupBy('product_name')
                        ->orderByDesc('total_revenue')
                        ->limit(10)
                        ->get(),
                ];
            });
        } catch (\Exception $e) {
            $reportData = [
                'total_sales' => 0, 'total_orders' => 0, 'orders_by_status' => collect(), 'daily_revenue' => collect(), 'top_products' => collect(),
            ];
        }

        return view('cms.orders.reports', array_merge($reportData, [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]));
    }
}
