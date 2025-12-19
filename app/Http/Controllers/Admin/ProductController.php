<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProjectBrand;
use App\Models\ProjectProduct;
use App\Models\ProjectProductCategory;
use App\Traits\HasAlerts;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use HasAlerts;

    public function index(Request $request)
    {
        // Multi-site: Always require project context
        $projectCode = request()->route('projectCode');
        if (! $projectCode) {
            abort(404, 'Project context required');
        }

        // Always use project models - Default to language_id = 1 (Vietnamese)
        $languageId = $request->get('language_id', 1);

        $products = ProjectProduct::with(['category', 'categories', 'brands'])
            ->where('language_id', $languageId)
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->category, fn ($q) => $q->where('product_category_id', $request->category))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        $parentCategories = ProjectProductCategory::whereNull('parent_id')->with('children')->get();

        // Pass current project context to view
        $currentProject = (object) ['code' => $projectCode];

        return view('admin.products.index', compact('products', 'parentCategories', 'currentProject', 'languageId'));
    }

    public function create(Request $request)
    {
        // Multi-site: Always require project context
        $projectCode = request()->route('projectCode');
        if (! $projectCode) {
            abort(404, 'Project context required');
        }

        // Get all categories for multi-select
        $categories = ProjectProductCategory::orderBy('sort_order')->get();
        $categoriesTree = $this->getCategoriesTree(); // For dropdown if needed

        // Categories loaded successfully
        $attributes = ProductAttribute::with('values')->get();
        $brands = ProjectBrand::orderBy('name')->get();

        // Lấy ngôn ngữ hiện tại từ URL parameter
        $languages = setting('languages', []);
        $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';
        $currentLang = $request->get('lang', $defaultLang);

        // Lưu ngôn ngữ hiện tại vào session
        session(['admin_language' => $currentLang]);

        return view('cms.products.create', compact('categories', 'categoriesTree', 'attributes', 'brands', 'currentLang'));
    }

    // Removed legacy CMS model methods - now always use Project models

    private function getCategoriesTree()
    {
        $allCategories = ProjectProductCategory::orderBy('sort_order')->get();

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
        $projectCode = request()->route('projectCode');

        // Validation đơn giản - chỉ 2 trường bắt buộc
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100',
        ]);

        // Lấy tất cả dữ liệu từ form và set defaults
        $validated['slug'] = $request->input('slug') ?: Str::slug($validated['name']);
        $validated['description'] = $request->input('description', '');
        $validated['short_description'] = $request->input('short_description');
        $validated['status'] = $request->input('status', 'draft');
        $validated['language_id'] = 1;
        $validated['stock_quantity'] = $request->input('stock_quantity', 0);
        $validated['stock_status'] = $request->input('stock_status', 'in_stock');
        $validated['product_type'] = 'simple';
        $validated['views'] = 0;
        $validated['rating_average'] = 0.00;
        $validated['rating_count'] = 0;

        // Boolean fields
        $validated['is_featured'] = $request->has('is_featured');
        $validated['noindex'] = $request->has('noindex');
        $validated['manage_stock'] = $request->has('manage_stock');

        // Optional fields từ form
        if ($request->filled('price')) {
            $validated['price'] = $request->input('price');
            $validated['has_price'] = true;
        } else {
            $validated['has_price'] = false;
        }

        if ($request->filled('sale_price')) {
            $validated['sale_price'] = $request->input('sale_price');
        }

        if ($request->filled('meta_title')) {
            $validated['meta_title'] = $request->input('meta_title');
        }

        if ($request->filled('meta_description')) {
            $validated['meta_description'] = $request->input('meta_description');
        }

        if ($request->filled('focus_keyword')) {
            $validated['focus_keyword'] = $request->input('focus_keyword');
        }

        // Featured image - lưu URL trực tiếp
        if ($request->filled('featured_image')) {
            $validated['featured_image'] = $request->input('featured_image');
        }

        // Gallery - lưu dưới dạng array (model sẽ tự cast thành JSON)
        if ($request->has('gallery') && is_array($request->gallery)) {
            $validated['gallery'] = $request->gallery;
        }

        // Lưu sản phẩm vào database
        $product = ProjectProduct::create($validated);

        // Handle categories - sync với pivot table
        if ($request->has('categories') && is_array($request->categories) && count($request->categories) > 0) {
            $product->categories()->sync($request->categories);
            // Set primary category (first one selected)
            $product->update(['product_category_id' => $request->categories[0]]);
        }

        // Handle brands - sync với pivot table
        if ($request->has('brands') && is_array($request->brands) && count($request->brands) > 0) {
            $product->brands()->sync($request->brands);
            // Set primary brand (first one selected)
            $product->update(['brand_id' => $request->brands[0]]);
        }

        return redirect()->route('project.admin.products.index', $projectCode)->with('alert', [
            'type' => 'success',
            'message' => 'Thêm sản phẩm thành công!',
        ]);
    }

    // Removed legacy CMS product model method - now always use ProjectProduct

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

            $createdProducts[] = ProjectProduct::create($productData);

        }

        $projectCode = request()->route('projectCode');

        // dd($projectCode);
        return redirect()->route('project.admin.products.index', $projectCode)->with('alert', [
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

        $projectCode = request()->route('projectCode');

        return redirect()->route('project.admin.products.create', [$projectCode, 'lang' => $currentLang])->with('alert', [
            'type' => 'success',
            'message' => "Thêm sản phẩm bản {$language} thành công!",
        ]);
    }

    private function generateUniqueSlug($baseSlug, $language, $id = null)
    {
        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $query = ProjectProduct::where('slug', $slug)->where('language', $language);
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
            $query = ProjectProduct::where('sku', $sku);
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

    public function show($projectCode, $id)
    {
        $product = ProjectProduct::findOrFail($id);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Request $request, $projectCode, $id)
    {
        // Multi-site: Always require project context
        if (! $projectCode) {
            abort(404, 'Project context required');
        }

        $product = ProjectProduct::with(['categories', 'brands'])->findOrFail($id);

        // Get all categories for multi-select
        $categories = ProjectProductCategory::orderBy('sort_order')->get();
        $categoriesTree = $this->getCategoriesTree();
        $parentCategories = ProjectProductCategory::whereNull('parent_id')->with('children')->get();
        $brands = ProjectBrand::orderBy('name')->get();

        // Get current language (default to Vietnamese)
        $currentLang = $request->get('lang', 'vi');

        return view('cms.products.edit', compact('product', 'categories', 'categoriesTree', 'parentCategories', 'brands', 'currentLang'));
    }

    public function update(Request $request, $projectCode, $id)
    {
        $product = ProjectProduct::findOrFail($id);

        // Validation đơn giản - chỉ 2 trường bắt buộc (giống store)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100',
        ]);

        // Lấy tất cả dữ liệu từ form và set defaults
        $validated['slug'] = $request->input('slug') ?: Str::slug($validated['name']);
        $validated['description'] = $request->input('description', '');
        $validated['short_description'] = $request->input('short_description');
        $validated['status'] = $request->input('status', $product->status ?? 'draft');
        $validated['language_id'] = $product->language_id ?? 1;
        $validated['stock_quantity'] = $request->input('stock_quantity', $product->stock_quantity ?? 0);
        $validated['stock_status'] = $request->input('stock_status', $product->stock_status ?? 'in_stock');
        $validated['product_type'] = $request->input('product_type', $product->product_type ?? 'simple');

        // Boolean fields
        $validated['is_featured'] = $request->has('is_featured');
        $validated['noindex'] = $request->has('noindex');
        $validated['manage_stock'] = $request->has('manage_stock');

        // Optional fields từ form
        if ($request->filled('price')) {
            $validated['price'] = $request->input('price');
            $validated['has_price'] = true;
        } else {
            $validated['has_price'] = false;
        }

        if ($request->filled('sale_price')) {
            $validated['sale_price'] = $request->input('sale_price');
        }

        if ($request->filled('meta_title')) {
            $validated['meta_title'] = $request->input('meta_title');
        }

        if ($request->filled('meta_description')) {
            $validated['meta_description'] = $request->input('meta_description');
        }

        if ($request->filled('focus_keyword')) {
            $validated['focus_keyword'] = $request->input('focus_keyword');
        }

        // Featured image - lưu URL trực tiếp
        if ($request->filled('featured_image')) {
            $validated['featured_image'] = $request->input('featured_image');
        }

        // Gallery - lưu dưới dạng array (model sẽ tự cast thành JSON)
        if ($request->has('gallery') && is_array($request->gallery)) {
            $validated['gallery'] = $request->gallery;
        }

        // Cập nhật sản phẩm
        $product->update($validated);

        // Handle categories - sync với pivot table
        if ($request->has('categories') && is_array($request->categories) && count($request->categories) > 0) {
            $product->categories()->sync($request->categories);
            $product->update(['product_category_id' => $request->categories[0]]);
        } else {
            $product->categories()->sync([]);
            $product->update(['product_category_id' => null]);
        }

        // Handle brands - sync với pivot table
        if ($request->has('brands') && is_array($request->brands) && count($request->brands) > 0) {
            $product->brands()->sync($request->brands);
            $product->update(['brand_id' => $request->brands[0]]);
        } else {
            $product->brands()->sync([]);
            $product->update(['brand_id' => null]);
        }

        return redirect()->route('project.admin.products.index', $projectCode)->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật sản phẩm thành công!',
        ]);
    }

    private function updateSingle(Request $request, $product)
    {
        $projectCode = request()->route('projectCode');
        $tableName = $product->getTable();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => "required|string|unique:{$tableName},sku,{$product->id}",
            'slug' => "nullable|string|unique:{$tableName},slug,{$product->id}",
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
            'stock_quantity' => 'nullable|integer|min:0',
            'manage_stock' => 'boolean',
            'stock_status' => 'nullable|in:in_stock,out_of_stock',
        ]);

        // Set default language for single language mode
        $validated['language'] = 'vi';
        $validated['slug'] ??= Str::slug($validated['name']);
        $validated['is_featured'] = $request->has('is_featured');
        $validated['noindex'] = $request->has('noindex');
        $validated['manage_stock'] = $request->has('manage_stock');

        $product->update($validated);

        // Multi-site: Always redirect to project route
        return redirect()->route('project.admin.products.edit', [$projectCode, $product->id])->with('alert', [
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

        $projectCode = request()->route('projectCode');

        return redirect()->route('project.admin.products.edit', [$projectCode, $product->id])->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật sản phẩm thành công!',
        ]);
    }

    private function updateLanguageVersion(Request $request, ProjectProduct $product)
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
        $languageProduct = ProjectProduct::where('sku', $product->sku)
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

            ProjectProduct::create($validated);

            $message = "Tạo sản phẩm bản {$language} thành công!";
        }

        $currentLang = $request->get('lang', $language);

        $projectCode = request()->route('projectCode');

        return redirect()->route('project.admin.products.edit', [$projectCode, $product->id, 'lang' => $currentLang])->with('alert', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    public function quickEdit($id)
    {
        $product = ProjectProduct::findOrFail($id);

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
            \Log::info('Bulk Edit Request:', ['ids' => $ids]);

            $products = ProjectProduct::with(['category', 'brand', 'categories', 'brands'])->whereIn('id', $ids)->get();
            $categories = ProjectProductCategory::all(['id', 'name']);
            $brands = ProjectBrand::all(['id', 'name']);

            // Add selected categories and brands to each product for the UI
            $products = $products->map(function ($product) {
                $product->selected_categories = $product->categories->pluck('id')->toArray();
                $product->selected_brands = $product->brands->pluck('id')->toArray();

                \Log::info("Product {$product->id} relationships:", [
                    'categories' => $product->categories->pluck('name')->toArray(),
                    'brands' => $product->brands->pluck('name')->toArray(),
                    'selected_categories' => $product->selected_categories,
                    'selected_brands' => $product->selected_brands,
                ]);

                return $product;
            });

            return response()->json([
                'success' => true,
                'products' => $products,
                'categories' => $categories,
                'brands' => $brands,
            ]);
        } catch (\Exception $e) {
            \Log::error('Bulk Edit Error:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi: '.$e->getMessage(),
            ], 500);
        }
    }

    public function bulkUpdate(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            $categories = $request->input('categories', []);
            $brands = $request->input('brands', []);
            $badges = $request->input('badges', []);
            $status = $request->input('status');
            $price = $request->input('price');
            $salePrice = $request->input('sale_price');

            // Debug logging
            \Log::info('Bulk Update Request Data:', [
                'ids' => $ids,
                'categories' => $categories,
                'brands' => $brands,
                'badges' => $badges,
                'status' => $status,
                'price' => $price,
                'sale_price' => $salePrice,
            ]);

            // Validate price and sale_price relationship
            if (! empty($price) && ! empty($salePrice)) {
                if (floatval($salePrice) >= floatval($price)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Giá khuyến mãi phải nhỏ hơn giá gốc!',
                    ], 422);
                }
            }

            $updateData = [];

            // Prepare single field updates
            if (! empty($status)) {
                $updateData['status'] = $status;
            }

            if (! empty($price)) {
                $updateData['price'] = $price;
            }

            if (! empty($salePrice)) {
                $updateData['sale_price'] = $salePrice;
            }

            // Update single fields for all products
            if (! empty($updateData)) {
                ProjectProduct::whereIn('id', $ids)->update($updateData);
                \Log::info('Updated single fields for products', ['ids' => $ids, 'data' => $updateData]);
            }

            // Validate sale price against existing prices if only sale_price is being updated
            if (empty($price) && ! empty($salePrice)) {
                $products = ProjectProduct::whereIn('id', $ids)->get();
                foreach ($products as $product) {
                    if ($product->price && floatval($salePrice) >= floatval($product->price)) {
                        return response()->json([
                            'success' => false,
                            'message' => "Giá khuyến mãi ({$salePrice}) phải nhỏ hơn giá gốc hiện tại của sản phẩm '{$product->name}' ({$product->price})!",
                        ], 422);
                    }
                }
            }

            // Update categories and brands for each product individually
            foreach ($ids as $productId) {
                $product = ProjectProduct::find($productId);
                if ($product) {
                    // Update multiple categories using pivot table
                    if (! empty($categories)) {
                        $product->categories()->sync($categories);
                        // Also update primary category for backward compatibility
                        $product->update(['product_category_id' => $categories[0]]);
                        \Log::info("Synced categories for product {$productId}", ['categories' => $categories]);
                    }

                    // Update multiple brands using pivot table
                    if (! empty($brands)) {
                        $product->brands()->sync($brands);
                        // Also update primary brand for backward compatibility
                        $product->update(['brand_id' => $brands[0]]);
                        \Log::info("Synced brands for product {$productId}", ['brands' => $brands]);
                    }

                    // Update badges if selected
                    if (! empty($badges)) {
                        $badgeUpdates = [];
                        $currentBadges = $product->badges ?? [];

                        // Badge configurations
                        $badgeConfigs = [
                            'featured' => [
                                'type' => 'featured',
                                'label' => 'Nổi bật',
                                'color' => 'yellow',
                                'icon' => 'star',
                            ],
                            'favorite' => [
                                'type' => 'favorite',
                                'label' => 'Yêu thích',
                                'color' => 'red',
                                'icon' => 'heart',
                            ],
                            'bestseller' => [
                                'type' => 'bestseller',
                                'label' => 'Bán chạy',
                                'color' => 'green',
                                'icon' => 'trending-up',
                            ],
                        ];

                        // Update boolean fields
                        foreach (['featured', 'favorite', 'bestseller'] as $badgeType) {
                            $fieldName = 'is_'.$badgeType;
                            if (in_array($badgeType, $badges)) {
                                $badgeUpdates[$fieldName] = true;

                                // Add to badges JSON if not exists
                                $badgeExists = false;
                                foreach ($currentBadges as $badge) {
                                    if ($badge['type'] === $badgeType) {
                                        $badgeExists = true;
                                        break;
                                    }
                                }
                                if (! $badgeExists) {
                                    $currentBadges[] = $badgeConfigs[$badgeType];
                                }
                            } else {
                                $badgeUpdates[$fieldName] = false;

                                // Remove from badges JSON
                                $currentBadges = array_filter($currentBadges, function ($badge) use ($badgeType) {
                                    return $badge['type'] !== $badgeType;
                                });
                                $currentBadges = array_values($currentBadges);
                            }
                        }

                        // Update the product with new badge data
                        $badgeUpdates['badges'] = $currentBadges;
                        $product->update($badgeUpdates);
                        \Log::info("Updated badges for product {$productId}", ['badges' => $badges, 'updates' => $badgeUpdates]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công '.\count($ids).' sản phẩm!',
            ]);
        } catch (\Exception $e) {
            \Log::error('Bulk Update Error:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: '.$e->getMessage(),
            ], 500);
        }
    }

    public function toggleBadge(Request $request)
    {
        try {
            $productId = $request->input('product_id');
            $badgeType = $request->input('badge_type');

            // Validate inputs
            if (! $productId || ! $badgeType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thiếu thông tin sản phẩm hoặc loại badge',
                ], 400);
            }

            // Validate badge type
            $validBadgeTypes = ['featured', 'favorite', 'bestseller'];
            if (! in_array($badgeType, $validBadgeTypes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Loại badge không hợp lệ',
                ], 400);
            }

            $product = ProjectProduct::findOrFail($productId);

            // Map badge types to database fields
            $fieldMap = [
                'featured' => 'is_featured',
                'favorite' => 'is_favorite',
                'bestseller' => 'is_bestseller',
            ];

            $field = $fieldMap[$badgeType];
            $currentValue = $product->$field;
            $newValue = ! $currentValue;

            // Update the boolean field
            $product->update([$field => $newValue]);

            // Update badges JSON field
            $badges = $product->badges ?? [];

            // Badge configurations
            $badgeConfigs = [
                'featured' => [
                    'type' => 'featured',
                    'label' => 'Nổi bật',
                    'color' => 'yellow',
                    'icon' => 'star',
                ],
                'favorite' => [
                    'type' => 'favorite',
                    'label' => 'Yêu thích',
                    'color' => 'red',
                    'icon' => 'heart',
                ],
                'bestseller' => [
                    'type' => 'bestseller',
                    'label' => 'Bán chạy',
                    'color' => 'green',
                    'icon' => 'trending-up',
                ],
            ];

            if ($newValue) {
                // Add badge to JSON if not exists
                $badgeExists = false;
                foreach ($badges as $badge) {
                    if ($badge['type'] === $badgeType) {
                        $badgeExists = true;
                        break;
                    }
                }

                if (! $badgeExists) {
                    $badges[] = $badgeConfigs[$badgeType];
                }
            } else {
                // Remove badge from JSON
                $badges = array_filter($badges, function ($badge) use ($badgeType) {
                    return $badge['type'] !== $badgeType;
                });
                $badges = array_values($badges); // Re-index array
            }

            // Update badges JSON field
            $product->update(['badges' => $badges]);

            $actionText = $newValue ? 'bật' : 'tắt';
            $badgeLabel = $badgeConfigs[$badgeType]['label'];

            return response()->json([
                'success' => true,
                'message' => "Đã {$actionText} badge '{$badgeLabel}' cho sản phẩm '{$product->name}'",
                'badge_active' => $newValue,
                'product_id' => $productId,
                'badge_type' => $badgeType,
            ]);

        } catch (\Exception $e) {
            \Log::error('Badge Toggle Error:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật badge: '.$e->getMessage(),
            ], 500);
        }
    }

    public function quickUpdate(Request $request, $id)
    {
        $product = ProjectProduct::findOrFail($id);

        // Get the correct table name for validation
        $tableName = $product->getTable();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => "required|string|unique:{$tableName},sku,{$product->id}",
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

    public function destroy($projectCode, $id)
    {
        $product = ProjectProduct::findOrFail($id);
        $product->delete();

        return redirect()->route('project.admin.products.index', $projectCode)->with('alert', [
            'type' => 'success',
            'message' => 'Xóa sản phẩm thành công!',
        ]);
    }
}
