<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttributeValueRequest extends FormRequest
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
            return $user->hasPermission('manage_attributes');
        }

        // Fallback to regular auth for non-project routes
        return auth()->check() && auth()->user()->hasPermission('manage_attributes');
    }

    public function rules()
    {
        return [
            'product_attribute_id' => 'required|exists:product_attributes,id',
            'value' => 'required|string|max:255',
            'display_value' => 'nullable|string|max:255',
            'color_code' => 'nullable|regex:/^#[0-9A-F]{6}$/i',
            'sort_order' => 'integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'product_attribute_id.required' => 'Thuộc tính là bắt buộc.',
            'value.required' => 'Giá trị thuộc tính là bắt buộc.',
            'color_code.regex' => 'Mã màu phải là hex color hợp lệ.',
        ];
    }
}
