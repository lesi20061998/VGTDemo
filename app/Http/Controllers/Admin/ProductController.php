<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Taxonomy;
use App\Models\ProductAttribute;
use App\Models\ProjectBrand;
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
        $currentProject = \App\Models\Project::where('code', request()->route('projectCode'))->first();
        $languageId = 1;

        return view('admin.products.index', compact('products', 'parentCategories', 'currentProject', 'languageId'));
    }

    public function create(Request $request)
    {
        $categories = Taxonomy::where('taxonomy', 'product_cat')->orderBy('order')->get();
        $categoriesTree = $this->buildCategoryOptions($categories); 
        $attributes = collect();
        try { $attributes = ProductAttribute::with('values')->orderBy('sort_order')->get(); } catch (\Exception $e) {}
        
        $brands = collect();
        try { $brands = ProjectBrand::orderBy('name')->get(); } catch (\Exception $e) {}
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
        $product = Post::where('post_type', 'product')->findOrFail($id);
        
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
        try { $attributes = ProductAttribute::with('values')->orderBy('sort_order')->get(); } catch (\Exception $e) {}
        
        $brands = collect();
        try { $brands = ProjectBrand::orderBy('name')->get(); } catch (\Exception $e) {}
        $currentLang = 'vi';

        // Mock relations for view
        $product->categories = $product->taxonomies;
        $product->brands = collect($brands)->whereIn('id', $metaData['brands'] ?? []);

        // Biến đổi attributes_data cho view
        $attributeMappings = collect();
        foreach ($product->attributes_data as $attrId => $values) {
            foreach ($values as $valId) {
                $attributeMappings->push((object)[
                    'product_attribute_id' => $attrId,
                    'product_attribute_value_id' => $valId
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

        $post = Post::where('post_type', 'product')->findOrFail($id);
        
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
        $post = Post::where('post_type', 'product')->findOrFail($id);
        $title = $post->title;
        $post->delete();

        $this->alertDeleted('sản phẩm', "Sản phẩm '{$title}' đã được xóa.");
        
        $route = request()->route('projectCode') 
            ? route('project.admin.products.index', request()->route('projectCode')) 
            : route('cms.products.index');
            
        return redirect($route);
    }
}
