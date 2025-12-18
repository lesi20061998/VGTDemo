<?php

// MODIFIED: 2025-01-21

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
            return $user->hasPermission('manage_categories');
        }

        // Fallback to regular auth for non-project routes
        return auth()->check() && auth()->user()->hasPermission('manage_categories');
    }

    public function rules()
    {
        // Get category ID from route parameter
        $categoryId = null;
        if ($this->route('category')) {
            $categoryId = is_object($this->route('category')) 
                ? $this->route('category')->id 
                : $this->route('category');
        }
        
        // Determine table name based on context
        $tableName = 'product_categories';
        
        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique($tableName, 'slug')->ignore($categoryId),
            ],
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:500',
            'parent_id' => "nullable|exists:{$tableName},id",
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'slug.unique' => 'Slug đã tồn tại.',
            'parent_id.exists' => 'Danh mục cha không tồn tại.',
        ];
    }
}
