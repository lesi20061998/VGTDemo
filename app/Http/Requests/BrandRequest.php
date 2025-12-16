<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->can('manage brands');
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
                Rule::unique('brands', 'slug')->ignore($brandId)
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

