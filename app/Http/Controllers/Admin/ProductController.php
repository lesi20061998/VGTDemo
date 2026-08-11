<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\ProductAttribute;
use App\Models\Project;
use App\Models\ProjectBrand;
use App\Models\Taxonomy;
use App\Traits\HasCrudAlerts;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use HasCrudAlerts;

    public function index(Request $request)
    {
        $products = Post::where('post_type', 'product')
            ->with(['taxonomies'])
            ->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        $parentCategories = Taxonomy::where('taxonomy', 'product_cat')->whereNull('parent_id')->with('children')->get();
        $currentProject = Project::where('code', request()->route('projectCode'))->first();
        $languageId = 1;

        return view('cms.products.index', compact('products', 'parentCategories', 'currentProject', 'languageId'));
    }

    public function create(Request $request)
    {
        $categories = Taxonomy::where('taxonomy', 'product_cat')->orderBy('order')->get();
        $categoriesTree = $this->buildCategoryOptions($categories);
        $attributes = collect();
        try {
            $attributes = ProductAttribute::with('values')->orderBy('sort_order')->get();
        } catch (\Exception $e) {
        }

        $brands = collect();
        try {
            $brands = ProjectBrand::orderBy('name')->get();
        } catch (\Exception $e) {
        }
        $currentLang = 'vi';

        return view('cms.products.create', compact('categories', 'categoriesTree', 'attributes', 'brands', 'currentLang'));
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:draft,published,archived',
        ]);

        $slug = $request->input('slug') ?: Str::slug($request->name);

        $metaData = [
            'sku' => $request->input('sku'),
            'price' => $request->input('price', 0),
            'sale_price' => $request->input('sale_price', 0),
            'product_type' => $request->input('product_type', 'simple'),
            'stock_quantity' => $request->input('stock_quantity', 0),
            'manage_stock' => $request->has('manage_stock'),
            'is_featured' => $request->has('is_featured'),
            'attributes' => $request->input('attributes', []),
            'variations' => $request->input('variations', []),
            'gallery' => $request->input('gallery', []),
            'brands' => $request->input('brands', []),
        ];

        $post = Post::create([
            'title' => $request->name,
            'slug' => $slug,
            'excerpt' => $request->short_description,
            'content' => $request->description,
            'featured_image' => $request->input('featured_image'),
            'post_type' => 'product',
            'status' => $request->status,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'seo_data' => [
                'focus_keyword' => $request->focus_keyword,
                'noindex' => $request->has('noindex'),
            ],
            'meta_data' => $metaData,
        ]);

        if ($request->has('categories')) {
            $post->taxonomies()->sync($request->categories);
        }

        $this->alertCreated('sản phẩm', "Sản phẩm '{$post->title}' đã được thêm.");

        $route = request()->route('projectCode')
            ? route('project.admin.products.index', request()->route('projectCode'))
            : route('cms.products.index');

        return redirect($route);
    }

    public function edit(Request $request, $projectCode, $id)
    {
        $product = Post::where('post_type', 'product')
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('slug', $id);
            })->firstOrFail();

        // Map Post to view variables to minimize blade changes
        $product->name = $product->title;
        $product->description = $product->content;
        $product->short_description = $product->excerpt;

        $metaData = is_string($product->meta_data) ? json_decode($product->meta_data, true) : ($product->meta_data ?? []);
        $product->sku = $metaData['sku'] ?? '';
        $product->price = $metaData['price'] ?? 0;
        $product->sale_price = $metaData['sale_price'] ?? 0;
        $product->product_type = $metaData['product_type'] ?? 'simple';
        $product->stock_quantity = $metaData['stock_quantity'] ?? 0;
        $product->manage_stock = $metaData['manage_stock'] ?? false;
        $product->is_featured = $metaData['is_featured'] ?? false;
        $product->gallery = $metaData['gallery'] ?? [];
        $product->attributes_data = $metaData['attributes'] ?? [];
        $product->variations_data = $metaData['variations'] ?? [];

        $seoData = is_string($product->seo_data) ? json_decode($product->seo_data, true) : ($product->seo_data ?? []);
        $product->focus_keyword = $seoData['focus_keyword'] ?? '';
        $product->noindex = $seoData['noindex'] ?? false;

        $categories = Taxonomy::where('taxonomy', 'product_cat')->orderBy('order')->get();
        $categoriesTree = $this->buildCategoryOptions($categories);
        $attributes = collect();
        try {
            $attributes = ProductAttribute::with('values')->orderBy('sort_order')->get();
        } catch (\Exception $e) {
        }

        $brands = collect();
        try {
            $brands = ProjectBrand::orderBy('name')->get();
        } catch (\Exception $e) {
        }
        $currentLang = 'vi';

        // Mock relations for view
        $product->categories = $product->taxonomies;
        $product->brands = collect($brands)->whereIn('id', $metaData['brands'] ?? []);

        // Biến đổi attributes_data cho view
        $attributeMappings = collect();
        foreach ($product->attributes_data as $attrId => $values) {
            foreach ($values as $valId) {
                $attributeMappings->push((object) [
                    'product_attribute_id' => $attrId,
                    'product_attribute_value_id' => $valId,
                ]);
            }
        }
        $product->attributeMappings = $attributeMappings;
        $product->variations = collect($product->variations_data);

        return view('cms.products.edit', compact('product', 'categories', 'categoriesTree', 'attributes', 'brands', 'currentLang'));
    }

    public function update(Request $request, $projectCode, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:draft,published,archived',
        ]);

        $post = Post::where('post_type', 'product')
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('slug', $id);
            })->firstOrFail();

        $slug = $request->input('slug') ?: Str::slug($request->name);

        $metaData = [
            'sku' => $request->input('sku'),
            'price' => $request->input('price', 0),
            'sale_price' => $request->input('sale_price', 0),
            'product_type' => $request->input('product_type', 'simple'),
            'stock_quantity' => $request->input('stock_quantity', 0),
            'manage_stock' => $request->has('manage_stock'),
            'is_featured' => $request->has('is_featured'),
            'attributes' => $request->input('attributes', []),
            'variations' => $request->input('variations', []),
            'gallery' => $request->input('gallery', []),
            'brands' => $request->input('brands', []),
        ];

        $post->update([
            'title' => $request->name,
            'slug' => $slug,
            'excerpt' => $request->short_description,
            'content' => $request->description,
            'featured_image' => $request->input('featured_image'),
            'status' => $request->status,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'seo_data' => [
                'focus_keyword' => $request->focus_keyword,
                'noindex' => $request->has('noindex'),
            ],
            'meta_data' => $metaData,
        ]);

        if ($request->has('categories')) {
            $post->taxonomies()->sync($request->categories);
        } else {
            $post->taxonomies()->sync([]);
        }

        $this->alertUpdated('sản phẩm', "Sản phẩm '{$post->title}' đã được cập nhật.");

        $route = request()->route('projectCode')
            ? route('project.admin.products.index', request()->route('projectCode'))
            : route('cms.products.index');

        return redirect($route);
    }

    public function destroy($projectCode, $id)
    {
        $post = Post::where('post_type', 'product')
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('slug', $id);
            })->firstOrFail();
        $title = $post->title;
        $post->delete();

        $this->alertDeleted('sản phẩm', "Sản phẩm '{$title}' đã được xóa.");

        $route = request()->route('projectCode')
            ? route('project.admin.products.index', request()->route('projectCode'))
            : route('cms.products.index');

        return redirect($route);
    }

    public function bulkEdit(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Không có sản phẩm nào được chọn']);
        }

        $products = Post::where('post_type', 'product')
            ->where(function ($q) use ($ids) {
                $q->whereIn('id', $ids)->orWhereIn('slug', $ids);
            })->get()->map(function ($p) {
                $metaData = is_string($p->meta_data) ? json_decode($p->meta_data, true) : ($p->meta_data ?? []);

                return [
                    'id' => $p->id,
                    'name' => $p->title,
                    'sku' => $metaData['sku'] ?? '',
                    'price' => $metaData['price'] ?? 0,
                    'sale_price' => $metaData['sale_price'] ?? 0,
                    'stock_quantity' => $metaData['stock_quantity'] ?? 0,
                ];
            });

        $categories = Taxonomy::where('taxonomy', 'product_cat')->get()->map(function ($c) {
            return ['id' => $c->id, 'name' => $c->name];
        });

        $brands = [];
        try {
            $brands = ProjectBrand::get()->map(function ($b) {
                return ['id' => $b->id, 'name' => $b->name];
            });
        } catch (\Exception $e) {
        }

        return response()->json([
            'success' => true,
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }

    public function bulkUpdate(Request $request)
    {
        $ids = $request->input('ids', []);
        $productsData = $request->input('products', []);

        if (empty($ids) && empty($productsData)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn ít nhất một sản phẩm để cập nhật.']);
        }

        if (! empty($productsData)) {
            foreach ($productsData as $data) {
                $post = Post::where('post_type', 'product')
                    ->where(function ($q) use ($data) {
                        $q->where('id', $data['id'])->orWhere('slug', $data['id']);
                    })->first();

                if ($post) {
                    $metaData = is_string($post->meta_data) ? json_decode($post->meta_data, true) : ($post->meta_data ?? []);

                    if (array_key_exists('sku', $data)) {
                        $metaData['sku'] = $data['sku'];
                    }
                    if (array_key_exists('price', $data)) {
                        $metaData['price'] = $data['price'] !== null && $data['price'] !== '' ? (float) $data['price'] : ($metaData['price'] ?? 0);
                    }
                    if (array_key_exists('sale_price', $data)) {
                        $metaData['sale_price'] = $data['sale_price'] !== null && $data['sale_price'] !== '' ? (float) $data['sale_price'] : null;
                    }
                    if (array_key_exists('stock_quantity', $data)) {
                        $metaData['stock_quantity'] = (int) $data['stock_quantity'];
                    }

                    if (! empty($data['name'])) {
                        $post->title = $data['name'];
                    }
                    if (! empty($data['status'])) {
                        $post->status = $data['status'];
                    }

                    $post->meta_data = $metaData;
                    $post->save();

                    if (isset($data['categories']) && is_array($data['categories'])) {
                        $post->taxonomies()->sync($data['categories']);
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Đã cập nhật thành công']);
        }

        $priceMode = $request->input('price_mode', 'no_change');
        $priceValue = (float) $request->input('price_value', 0);
        $salePriceMode = $request->input('sale_price_mode', 'no_change');
        $salePriceValue = (float) $request->input('sale_price_value', 0);
        $status = $request->input('status');
        $categories = $request->input('categories', []);

        $posts = Post::where('post_type', 'product')
            ->where(function ($q) use ($ids) {
                $q->whereIn('id', $ids)->orWhereIn('slug', $ids);
            })->get();

        foreach ($posts as $post) {
            $metaData = is_string($post->meta_data) ? json_decode($post->meta_data, true) : ($post->meta_data ?? []);
            $currentPrice = (float) ($metaData['price'] ?? 0);
            $currentSalePrice = isset($metaData['sale_price']) && $metaData['sale_price'] !== null ? (float) $metaData['sale_price'] : null;

            if ($priceMode === 'fixed') {
                $metaData['price'] = max(0, $priceValue);
            } elseif ($priceMode === 'percent_increase') {
                $metaData['price'] = max(0, round($currentPrice * (1 + $priceValue / 100)));
            } elseif ($priceMode === 'percent_decrease') {
                $metaData['price'] = max(0, round($currentPrice * (1 - $priceValue / 100)));
            } elseif ($priceMode === 'amount_increase') {
                $metaData['price'] = max(0, round($currentPrice + $priceValue));
            } elseif ($priceMode === 'amount_decrease') {
                $metaData['price'] = max(0, round($currentPrice - $priceValue));
            }

            $newPrice = (float) ($metaData['price'] ?? 0);

            if ($salePriceMode === 'fixed') {
                $metaData['sale_price'] = max(0, $salePriceValue);
            } elseif ($salePriceMode === 'percent_decrease_regular') {
                $metaData['sale_price'] = max(0, round($newPrice * (1 - $salePriceValue / 100)));
            } elseif ($salePriceMode === 'percent_increase') {
                $base = $currentSalePrice !== null ? $currentSalePrice : $currentPrice;
                $metaData['sale_price'] = max(0, round($base * (1 + $salePriceValue / 100)));
            } elseif ($salePriceMode === 'percent_decrease') {
                $base = $currentSalePrice !== null ? $currentSalePrice : $currentPrice;
                $metaData['sale_price'] = max(0, round($base * (1 - $salePriceValue / 100)));
            } elseif ($salePriceMode === 'amount_increase') {
                $base = $currentSalePrice !== null ? $currentSalePrice : $currentPrice;
                $metaData['sale_price'] = max(0, round($base + $salePriceValue));
            } elseif ($salePriceMode === 'amount_decrease') {
                $base = $currentSalePrice !== null ? $currentSalePrice : $currentPrice;
                $metaData['sale_price'] = max(0, round($base - $salePriceValue));
            } elseif ($salePriceMode === 'clear') {
                unset($metaData['sale_price']);
            }

            if (! empty($status)) {
                $post->status = $status;
            }

            $post->meta_data = $metaData;
            $post->save();

            if (! empty($categories) && is_array($categories)) {
                $post->taxonomies()->sync($categories);
            }
        }

        return response()->json(['success' => true, 'message' => 'Cập nhật sản phẩm thành công']);
    }

    public function toggleBadge(Request $request)
    {
        $productId = $request->input('product_id');
        $badgeType = $request->input('badge_type');
        $status = $request->input('status');

        $post = Post::where('post_type', 'product')
            ->where(function ($q) use ($productId) {
                $q->where('id', $productId)->orWhere('slug', $productId);
            })->first();
        if (! $post) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
        }

        $metaData = is_string($post->meta_data) ? json_decode($post->meta_data, true) : ($post->meta_data ?? []);

        $newState = false;
        if ($badgeType === 'featured') {
            $newState = ! ($metaData['is_featured'] ?? false);
            $metaData['is_featured'] = $newState;
        } elseif ($badgeType === 'favorite') {
            $newState = ! ($metaData['is_favorite'] ?? false);
            $metaData['is_favorite'] = $newState;
        }

        $post->meta_data = $metaData;
        $post->save();

        return response()->json(['success' => true, 'state' => $newState]);
    }
}
