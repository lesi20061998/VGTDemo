<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttributeValueRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->can('manage attributes');
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

