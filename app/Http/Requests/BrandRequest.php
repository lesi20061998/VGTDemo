<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandRequest extends FormRequest
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
            return $user->hasPermission('manage_brands');
        }

        // Fallback to regular auth for non-project routes
        return auth()->check() && auth()->user()->hasPermission('manage_brands');
    }

    public function rules()
    {
        $brandId = $this->route('brand')?->id;

        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('brands', 'slug')->ignore($brandId),
            ],
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tên nhà sản xuất là bắt buộc.',
            'slug.unique' => 'Slug nhà sản xuất đã tồn tại.',
        ];
    }
}
