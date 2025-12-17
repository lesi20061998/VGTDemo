<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductCategory;
use App\Traits\HasAlerts;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use HasAlerts;

    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->category, fn ($q) => $q->where('product_category_id', $request->category))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        $parentCategories = ProductCategory::whereNull('parent_id')->with('children')->get();

        return view('cms.products.index', compact('products', 'parentCategories'));
    }

    public function create(Request $request)
    {
        $categories = $this->getCategoriesTree();
        $attributes = ProductAttribute::with('values')->get();

        // Lấy ngôn ngữ hiện tại từ URL parameter
        $languages = setting('languages', []);
        $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';
        $currentLang = $request->get('lang', $defaultLang);

        // Lưu ngôn ngữ hiện tại vào session
        session(['admin_language' => $currentLang]);

        return view('cms.products.create', compact('categories', 'attributes', 'currentLang'));
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
            $options[] = (object) [
                'id' => $category->id,
                'name' => $prefix.$category->name,
            ];
            $childOptions = $this->buildCategoryOptions($categories, $category->id, $prefix.'  └─ ');
            $options = array_merge($options, $childOptions);
        }

        return $options;
    }

    public function store(Request $request)
    {
        $multilingualEnabled = setting('multilingual_enabled', false);

        if ($multilingualEnabled) {
            return $this->storeLanguageVersion($request);
        } else {
            return $this->storeSingle($request);
        }
    }

    private function storeSingle(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|unique:products_enhanced,sku',
            'slug' => 'nullable|string|unique:products_enhanced,slug',
            'price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'product_category_id' => 'required|exists:product_categories,id',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'product_type' => 'required|in:simple,variable',
            'featured_image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'focus_keyword' => 'nullable|string',
            'schema_type' => 'nullable|string',
            'canonical_url' => 'nullable|url',
            'noindex' => 'boolean',
            'language_versions' => 'required|array',
            'language_versions.*.name' => 'required|string|max:255',
            'language_versions.*.short_description' => 'nullable|string',
            'language_versions.*.description' => 'required|string',
            'language_versions.*.meta_title' => 'nullable|string|max:60',
            'language_versions.*.meta_description' => 'nullable|string|max:160',
        ]);

        $defaultLang = collect(setting('languages', []))->firstWhere('is_default', true)['code'] ?? 'vi';
        $langData = $request->input('language_versions')[$defaultLang];

        $validated['name'] = $langData['name'];
        $validated['short_description'] = $langData['short_description'];
        $validated['description'] = $langData['description'];
        $validated['meta_title'] = $langData['meta_title'];
        $validated['meta_description'] = $langData['meta_description'];
        $validated['language'] = $defaultLang;
        $validated['slug'] = $validated['slug'] ?? Str::slug($langData['name']);
        $validated['is_featured'] = $request->has('is_featured');
        $validated['noindex'] = $request->has('noindex');

        $product = Product::create($validated);

        return redirect()->route('cms.products.index')->with('alert', [
            'type' => 'success',
            'message' => 'Thêm sản phẩm thành công!',
        ]);
    }

    private function storeMultilingual(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string',
            'slug' => 'nullable|string',
            'price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'product_category_id' => 'required|exists:product_categories,id',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'product_type' => 'required|in:simple,variable',
            'featured_image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'focus_keyword' => 'nullable|string',
            'schema_type' => 'nullable|string',
            'canonical_url' => 'nullable|url',
            'noindex' => 'boolean',
            'language_versions' => 'required|array',
        ]);

        $languages = setting('languages', []);
        $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';

        // Validate ngôn ngữ mặc định
        $request->validate([
            "language_versions.{$defaultLang}.name" => 'required|string|max:255',
            "language_versions.{$defaultLang}.description" => 'required|string',
        ]);

        $validated['is_featured'] = $request->has('is_featured');
        $validated['noindex'] = $request->has('noindex');

        $createdProducts = [];

        foreach ($request->input('language_versions') as $langCode => $langData) {
            if (empty($langData['name']) && empty($langData['description'])) {
                continue; // Skip empty language versions
            }

            $productData = $validated;
            $productData['name'] = $langData['name'];
            $productData['short_description'] = $langData['short_description'];
            $productData['description'] = $langData['description'];
            $productData['meta_title'] = $langData['meta_title'];
            $productData['meta_description'] = $langData['meta_description'];
            $productData['language'] = $langCode;

            // Tạo slug riêng cho từng ngôn ngữ
            $baseSlug = $validated['slug'] ?? Str::slug($langData['name']);
            $productData['slug'] = $langCode === $defaultLang ? $baseSlug : $baseSlug.'-'.$langCode;

            // Tạo SKU riêng cho từng ngôn ngữ (trừ ngôn ngữ mặc định)
            $productData['sku'] = $langCode === $defaultLang ? $validated['sku'] : $validated['sku'].'-'.$langCode;

            $createdProducts[] = Product::create($productData);
        }

        return redirect()->route('cms.products.index')->with('alert', [
            'type' => 'success',
            'message' => 'Thêm sản phẩm đa ngôn ngữ thành công! Đã tạo '.count($createdProducts).' bản ghi.',
        ]);
    }

    private function storeLanguageVersion(Request $request)
    {
        $validated = $request->validate([
            'language' => 'required|string|in:'.implode(',', array_column(setting('languages', []), 'code')),
            'sku' => 'required|string',
            'slug' => 'nullable|string',
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'description' => 'required|string',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'product_category_id' => 'required|exists:product_categories,id',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'product_type' => 'required|in:simple,variable',
            'featured_image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'focus_keyword' => 'nullable|string',
            'schema_type' => 'nullable|string',
            'canonical_url' => 'nullable|url',
            'noindex' => 'boolean',
        ]);

        $language = $validated['language'];

        // Tạo slug unique cho ngôn ngữ này
        $baseSlug = $validated['slug'] ?? Str::slug($validated['name']);
        $slug = $this->generateUniqueSlug($baseSlug, $language);

        // Tạo SKU unique cho ngôn ngữ này
        $baseSku = $validated['sku'];
        $sku = $this->generateUniqueSku($baseSku, $language);

        $validated['slug'] = $slug;
        $validated['sku'] = $sku;
        $validated['is_featured'] = $request->has('is_featured');
        $validated['noindex'] = $request->has('noindex');

        $product = Product::create($validated);

        $currentLang = $request->get('lang', $language);

        return redirect()->route('cms.products.create', ['lang' => $currentLang])->with('alert', [
            'type' => 'success',
            'message' => "Thêm sản phẩm bản {$language} thành công!",
        ]);
    }

    private function generateUniqueSlug($baseSlug, $language, $id = null)
    {
        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $query = Product::where('slug', $slug)->where('language', $language);
            if ($id) {
                $query->where('id', '!=', $id);
            }

            if (! $query->exists()) {
                break;
            }

            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function generateUniqueSku($baseSku, $language, $id = null)
    {
        $sku = $baseSku.'-'.strtoupper($language);
        $counter = 1;

        while (true) {
            $query = Product::where('sku', $sku);
            if ($id) {
                $query->where('id', '!=', $id);
            }

            if (! $query->exists()) {
                break;
            }

            $sku = $baseSku.'-'.strtoupper($language).'-'.$counter;
            $counter++;
        }

        return $sku;
    }

    public function show(Product $product)
    {
        return view('cms.products.show', compact('product'));
    }

    public function edit(Request $request, Product $product)
    {
        $categories = $this->getCategoriesTree();
        $attributes = ProductAttribute::with('values')->get();

        $multilingualEnabled = setting('multilingual_enabled', false);

        if ($multilingualEnabled) {
            // Lấy ngôn ngữ hiện tại từ URL parameter
            $languages = setting('languages', []);
            $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';
            $currentLang = $request->get('lang', $product->language ?? $defaultLang);

            // Tìm bản ghi cho ngôn ngữ hiện tại
            $languageProduct = Product::where('sku', $product->sku)
                ->where('language', $currentLang)
                ->first();

            if (! $languageProduct) {
                // Nếu chưa có bản ghi cho ngôn ngữ này, tạo mới dựa trên bản gốc
                $languageProduct = new Product;
                $languageProduct->fill($product->toArray());
                $languageProduct->language = $currentLang;
                $languageProduct->name = '';
                $languageProduct->description = '';
                $languageProduct->short_description = '';
                $languageProduct->meta_title = '';
                $languageProduct->meta_description = '';
                $languageProduct->slug = '';
            }

            // Lấy tất cả các bản ghi ngôn ngữ khác để hiển thị status
            $allLanguageVersions = Product::where('sku', $product->sku)->get()->keyBy('language');

            session(['admin_language' => $currentLang]);

            return view('cms.products.edit', compact('languageProduct', 'categories', 'attributes', 'currentLang', 'allLanguageVersions', 'product'));
        }

        return view('cms.products.edit', compact('product', 'categories', 'attributes'));
    }

    public function update(Request $request, Product $product)
    {
        $multilingualEnabled = setting('multilingual_enabled', false);

        if ($multilingualEnabled) {
            return $this->updateLanguageVersion($request, $product);
        } else {
            return $this->updateSingle($request, $product);
        }
    }

    private function updateSingle(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'required|string|unique:products_enhanced,sku,'.$product->id,
            'slug' => 'nullable|string|unique:products_enhanced,slug,'.$product->id,
            'price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'product_category_id' => 'required|exists:product_categories,id',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'product_type' => 'required|in:simple,variable',
            'featured_image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'focus_keyword' => 'nullable|string',
            'schema_type' => 'nullable|string',
            'canonical_url' => 'nullable|url',
            'noindex' => 'boolean',
            'language_versions' => 'required|array',
            'language_versions.*.name' => 'required|string|max:255',
            'language_versions.*.short_description' => 'nullable|string',
            'language_versions.*.description' => 'required|string',
            'language_versions.*.meta_title' => 'nullable|string|max:60',
            'language_versions.*.meta_description' => 'nullable|string|max:160',
        ]);

        $defaultLang = collect(setting('languages', []))->firstWhere('is_default', true)['code'] ?? 'vi';
        $langData = $request->input('language_versions')[$defaultLang];

        $validated['name'] = $langData['name'];
        $validated['short_description'] = $langData['short_description'];
        $validated['description'] = $langData['description'];
        $validated['meta_title'] = $langData['meta_title'];
        $validated['meta_description'] = $langData['meta_description'];
        $validated['language'] = $defaultLang;
        $validated['slug'] = $validated['slug'] ?? Str::slug($langData['name']);
        $validated['is_featured'] = $request->has('is_featured');
        $validated['noindex'] = $request->has('noindex');

        $product->update($validated);

        return redirect()->route('cms.products.edit', $product)->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật sản phẩm thành công!',
        ]);
    }

    private function updateMultilingual(Request $request, Product $product)
    {
        // For multilingual, we need to handle updating the specific language version
        // This is more complex as we need to identify which language version we're editing

        $validated = $request->validate([
            'sku' => 'required|string',
            'slug' => 'nullable|string',
            'price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'product_category_id' => 'required|exists:product_categories,id',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'product_type' => 'required|in:simple,variable',
            'featured_image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'focus_keyword' => 'nullable|string',
            'schema_type' => 'nullable|string',
            'canonical_url' => 'nullable|url',
            'noindex' => 'boolean',
            'language_versions' => 'required|array',
        ]);

        $languages = setting('languages', []);
        $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';

        // Update the current product (assuming it's the default language version)
        $langData = $request->input('language_versions')[$product->language] ?? $request->input('language_versions')[$defaultLang];

        $validated['name'] = $langData['name'];
        $validated['short_description'] = $langData['short_description'];
        $validated['description'] = $langData['description'];
        $validated['meta_title'] = $langData['meta_title'];
        $validated['meta_description'] = $langData['meta_description'];
        $validated['is_featured'] = $request->has('is_featured');
        $validated['noindex'] = $request->has('noindex');

        $product->update($validated);

        return redirect()->route('cms.products.edit', $product)->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật sản phẩm thành công!',
        ]);
    }

    private function updateLanguageVersion(Request $request, Product $product)
    {
        $validated = $request->validate([
            'language' => 'required|string|in:'.implode(',', array_column(setting('languages', []), 'code')),
            'sku' => 'required|string',
            'slug' => 'nullable|string',
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'description' => 'required|string',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'product_category_id' => 'required|exists:product_categories,id',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'product_type' => 'required|in:simple,variable',
            'featured_image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'focus_keyword' => 'nullable|string',
            'schema_type' => 'nullable|string',
            'canonical_url' => 'nullable|url',
            'noindex' => 'boolean',
        ]);

        $language = $validated['language'];

        // Tìm bản ghi cho ngôn ngữ hiện tại
        $languageProduct = Product::where('sku', $product->sku)
            ->where('language', $language)
            ->first();

        if ($languageProduct) {
            // Cập nhật bản ghi hiện có
            $baseSlug = $validated['slug'] ?? Str::slug($validated['name']);
            $slug = $this->generateUniqueSlug($baseSlug, $language, $languageProduct->id);

            $validated['slug'] = $slug;
            $validated['is_featured'] = $request->has('is_featured');
            $validated['noindex'] = $request->has('noindex');

            $languageProduct->update($validated);

            $message = "Cập nhật sản phẩm bản {$language} thành công!";
        } else {
            // Tạo bản ghi mới cho ngôn ngữ này
            $baseSlug = $validated['slug'] ?? Str::slug($validated['name']);
            $slug = $this->generateUniqueSlug($baseSlug, $language);

            $baseSku = $validated['sku'];
            $sku = $this->generateUniqueSku($baseSku, $language);

            $validated['slug'] = $slug;
            $validated['sku'] = $sku;
            $validated['is_featured'] = $request->has('is_featured');
            $validated['noindex'] = $request->has('noindex');

            Product::create($validated);

            $message = "Tạo sản phẩm bản {$language} thành công!";
        }

        $currentLang = $request->get('lang', $language);

        return redirect()->route('cms.products.edit', [$product, 'lang' => $currentLang])->with('alert', [
            'type' => 'success',
            'message' => $message,
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
            'is_featured' => $product->is_featured,
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
                'brands' => $brands,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: '.$e->getMessage(),
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
        if (! empty($categories) && count($categories) > 0) {
            $updateData['product_category_id'] = $categories[0];
        }

        // Update brand (chỉ lấy brand đầu tiên)
        if (! empty($brands) && count($brands) > 0) {
            $updateData['brand_id'] = $brands[0];
        }

        if (! empty($status)) {
            $updateData['status'] = $status;
        }

        if (! empty($price)) {
            $updateData['price'] = $price;
        }

        if (! empty($salePrice)) {
            $updateData['sale_price'] = $salePrice;
        }

        if (! empty($updateData)) {
            Product::whereIn('id', $ids)->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công '.count($ids).' sản phẩm!',
        ]);
    }

    public function quickUpdate(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products_enhanced,sku,'.$product->id,
            'price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'stock_quantity' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
        ]);

        $validated['is_featured'] = $request->has('is_featured');

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật sản phẩm thành công!',
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('cms.products.index')->with('alert', [
            'type' => 'success',
            'message' => 'Xóa sản phẩm thành công!',
        ]);
    }
}
