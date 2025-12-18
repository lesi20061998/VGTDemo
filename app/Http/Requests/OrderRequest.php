<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        // For project routes, get user from request attributes (set by CheckCmsRole middleware)
        $user = $this->attributes->get('auth_user');

        if ($user) {
            // Super admin or admin level users have all permissions
            if (isset($user->level) && in_array($user->level, [0, 1])) {
                return true;
            }

            // Check if user has the specific permission
            return $user->hasPermission('manage_orders');
        }

        // Fallback to regular auth for non-project routes
        return auth()->check() && auth()->user()->hasPermission('manage_orders');
    }

    public function rules()
    {
        return [
            'order_number' => 'required|string|unique:orders|max:50',
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'currency' => 'string|size:3',
            'billing_address' => 'required|array',
            'shipping_address' => 'required|array',
            'payment_method' => 'nullable|string|max:100',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'customer_notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'order_number.required' => 'Số đơn hàng là bắt buộc.',
            'order_number.unique' => 'Số đơn hàng đã tồn tại.',
            'customer_name.required' => 'Tên khách hàng là bắt buộc.',
            'customer_email.required' => 'Email khách hàng là bắt buộc.',
            'total_amount.required' => 'Tổng tiền là bắt buộc.',
        ];
    }
}
