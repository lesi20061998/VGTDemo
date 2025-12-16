<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductAttribute;
use App\Traits\HasAlerts;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use HasAlerts;
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->category, fn($q) => $q->where('product_category_id', $request->category))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        $parentCategories = ProductCategory::whereNull('parent_id')->with('children')->get();

        return view('cms.products.index', compact('products', 'parentCategories'));
    }

    public function create()
    {
        $categories = $this->getCategoriesTree();
        $attributes = ProductAttribute::with('values')->get();
        
        return view('cms.products.create', compact('categories', 'attributes'));
    }
    
    private function getCategoriesTree()
    {
        $allCategories = ProductCategory::orderBy('sort_order')->get();
        return $this->buildCategoryOptions($allCategories);
    }
    
    private function buildCategoryOptions($categories, $parentId = null, $prefix = '')
    {
        $options = [];
        foreach ($categories->where('parent_id', $parentId) as $category) {
            $options[] = (object)[
                'id' => $category->id,
                'name' => $prefix . $category->name
            ];
            $childOptions = $this->buildCategoryOptions($categories, $category->id, $prefix . '  └─ ');
            $options = array_merge($options, $childOptions);
        }
        return $options;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:products_enhanced,slug',
            'sku' => 'required|string|unique:products_enhanced,sku',
            'short_description' => 'nullable|string',
            'description' => 'required|string',
            'price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'product_category_id' => 'required|exists:product_categories,id',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'product_type' => 'required|in:simple,variable',
            'featured_image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'focus_keyword' => 'nullable|string',
            'schema_type' => 'nullable|string',
            'canonical_url' => 'nullable|url',
            'noindex' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_featured'] = $request->has('is_featured');
        $validated['noindex'] = $request->has('noindex');

        $product = Product::create($validated);

        // Handle variations if variable product
        if ($validated['product_type'] === 'variable' && $request->has('variations')) {
            foreach ($request->variations as $variation) {
                $product->variations()->create($variation);
            }
        }

        return redirect()->route('cms.products.index')->with('alert', [
            'type' => 'success',
            'message' => 'Thêm sản phẩm thành công!'
        ]);
    }

    public function show(Product $product)
    {
        return view('cms.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = $this->getCategoriesTree();
        $attributes = ProductAttribute::with('values')->get();
        
        return view('cms.products.edit', compact('product', 'categories', 'attributes'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:products_enhanced,slug,' . $product->id,
            'sku' => 'required|string|unique:products_enhanced,sku,' . $product->id,
            'short_description' => 'nullable|string',
            'description' => 'required|string',
            'price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'product_category_id' => 'required|exists:product_categories,id',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'product_type' => 'required|in:simple,variable',
            'featured_image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'focus_keyword' => 'nullable|string',
            'schema_type' => 'nullable|string',
            'canonical_url' => 'nullable|url',
            'noindex' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_featured'] = $request->has('is_featured');
        $validated['noindex'] = $request->has('noindex');

        $product->update($validated);

        return redirect()->route('cms.products.edit', $product)->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật sản phẩm thành công!'
        ]);
    }

    public function quickEdit(Product $product)
    {
        return response()->json([
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => $product->price,
            'sale_price' => $product->sale_price,
            'stock_quantity' => $product->stock_quantity,
            'status' => $product->status,
            'is_featured' => $product->is_featured
        ]);
    }

    public function bulkEdit(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            $products = Product::with(['category', 'brand'])->whereIn('id', $ids)->get();
            $categories = ProductCategory::all(['id', 'name']);
            
            // Check if Brand model exists
            $brands = [];
            if (class_exists('\App\Models\Brand')) {
                $brands = \App\Models\Brand::all(['id', 'name']);
            }
            
            return response()->json([
                'success' => true,
                'products' => $products,
                'categories' => $categories,
                'brands' => $brands
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkUpdate(Request $request)
    {
        $ids = $request->input('ids', []);
        $categories = $request->input('categories', []);
        $brands = $request->input('brands', []);
        $status = $request->input('status');
        $price = $request->input('price');
        $salePrice = $request->input('sale_price');
        
        $updateData = [];
        
        // Update category (chỉ lấy category đầu tiên)
        if (!empty($categories) && count($categories) > 0) {
            $updateData['product_category_id'] = $categories[0];
        }
        
        // Update brand (chỉ lấy brand đầu tiên)
        if (!empty($brands) && count($brands) > 0) {
            $updateData['brand_id'] = $brands[0];
        }
        
        if (!empty($status)) {
            $updateData['status'] = $status;
        }
        
        if (!empty($price)) {
            $updateData['price'] = $price;
        }
        
        if (!empty($salePrice)) {
            $updateData['sale_price'] = $salePrice;
        }
        
        if (!empty($updateData)) {
            Product::whereIn('id', $ids)->update($updateData);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công ' . count($ids) . ' sản phẩm!'
        ]);
    }

    public function quickUpdate(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products_enhanced,sku,' . $product->id,
            'price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'stock_quantity' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean'
        ]);

        $validated['is_featured'] = $request->has('is_featured');
        
        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật sản phẩm thành công!'
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('cms.products.index')->with('alert', [
            'type' => 'success',
            'message' => 'Xóa sản phẩm thành công!'
        ]);
    }
}

