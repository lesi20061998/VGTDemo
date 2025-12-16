<?php
// MODIFIED: 2025-01-21

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->can('manage products');
    }

    public function rules()
    {
        $productId = $this->route('product')?->id;

        $allowed = config('cms.media.allowed_types', ['jpg','jpeg','png','gif','pdf','doc','docx']);
        $mimeExt = implode(',', array_map(fn($ext) => $ext, $allowed));

        // sizes are in KB in config/cms.php -> MEDIA_MAX_SIZE
        $maxKb = (int) config('cms.media.max_file_size', 2048);

        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products_enhanced', 'slug')->ignore($productId)
            ],
            'short_description' => 'nullable|string|max:500',
            'description' => 'required|string',
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products_enhanced', 'sku')->ignore($productId)
            ],
            'price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'has_price' => 'boolean',
            'stock_quantity' => 'integer|min:0',
            'manage_stock' => 'boolean',
            'stock_status' => 'required|in:in_stock,out_of_stock,on_backorder',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'featured_image' => ['nullable', 'file', 'mimes:'.$mimeExt, 'max:'.$maxKb],
            'images.*' => ['nullable', 'file', 'mimes:'.$mimeExt, 'max:'.$maxKb],
            'images' => ['nullable','array'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'sku.required' => 'Mã SKU là bắt buộc.',
            'sku.unique' => 'Mã SKU đã tồn tại.',
            'description.required' => 'Mô tả sản phẩm là bắt buộc.',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
        ];
    }
}
