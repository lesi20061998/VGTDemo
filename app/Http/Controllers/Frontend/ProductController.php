<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Taxonomy;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display product listing page
     */
    public function index(Request $request)
    {
        $query = Post::where('post_type', 'product')->where('status', 'published');
        
        // Filter by category
        if ($request->has('category')) {
            $category = Taxonomy::where('taxonomy', 'product_cat')->where('slug', $request->category)->first();
            if ($category) {
                $query->whereHas('taxonomies', function($q) use ($category) {
                    $q->where('term_taxonomy_id', $category->id);
                });
            }
        }
        
        // Filter by brand
        if ($request->has('brand')) {
            $query->where('meta_data->brands', 'like', '%"'.$request->brand.'"%');
        }
        
        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('meta_data->price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('meta_data->price', '<=', $request->max_price);
        }
        
        // Search
        if ($request->has('q') && $request->q) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->q}%")
                  ->orWhere('content', 'like', "%{$request->q}%");
            });
        }
        
        // Sort
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('meta_data->price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('meta_data->price', 'desc');
                break;
            case 'name':
                $query->orderBy('title', 'asc');
                break;
            case 'popular':
                $query->orderBy('views', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
        
        $products = $query->paginate(12);
        
        // Map data to maintain backward compatibility with views
        $products->getCollection()->transform(function($product) {
            $metaData = is_string($product->meta_data) ? json_decode($product->meta_data, true) : ($product->meta_data ?? []);
            $product->name = $product->title;
            $product->description = $product->content;
            $product->price = $metaData['price'] ?? 0;
            $product->sale_price = $metaData['sale_price'] ?? 0;
            return $product;
        });

        $categories = Taxonomy::where('taxonomy', 'product_cat')->orderBy('order')->get();
        
        return view('frontend.products.index', compact('products', 'categories'));
    }
    
    /**
     * Display products by category
     */
    public function category($categorySlug)
    {
        $category = Taxonomy::where('taxonomy', 'product_cat')->where('slug', $categorySlug)->firstOrFail();
        
        $products = Post::where('post_type', 'product')
            ->where('status', 'published')
            ->whereHas('taxonomies', function($q) use ($category) {
                $q->where('term_taxonomy_id', $category->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
        // Map data to maintain backward compatibility with views
        $products->getCollection()->transform(function($product) {
            $metaData = is_string($product->meta_data) ? json_decode($product->meta_data, true) : ($product->meta_data ?? []);
            $product->name = $product->title;
            $product->description = $product->content;
            $product->price = $metaData['price'] ?? 0;
            $product->sale_price = $metaData['sale_price'] ?? 0;
            return $product;
        });

        $categories = Taxonomy::where('taxonomy', 'product_cat')->orderBy('order')->get();
        
        return view('frontend.products.index', compact('products', 'categories', 'category'));
    }
    
    /**
     * Display single product detail
     */
    public function show($projectCode, $slug)
    {
        $product = Post::where('post_type', 'product')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
        
        // Increment views
        $product->increment('views');
        
        $metaData = is_string($product->meta_data) ? json_decode($product->meta_data, true) : ($product->meta_data ?? []);
        $product->name = $product->title;
        $product->description = $product->content;
        $product->short_description = $product->excerpt;
        $product->price = $metaData['price'] ?? 0;
        $product->sale_price = $metaData['sale_price'] ?? 0;
        $product->sku = $metaData['sku'] ?? '';
        $product->stock_quantity = $metaData['stock_quantity'] ?? 0;
        $product->gallery = $metaData['gallery'] ?? [];

        // Get related products (same category)
        $categoryIds = $product->taxonomies->pluck('id')->toArray();
        $relatedProducts = collect();
        if (!empty($categoryIds)) {
            $relatedProducts = Post::where('post_type', 'product')
                ->where('status', 'published')
                ->where('id', '!=', $product->id)
                ->whereHas('taxonomies', function($q) use ($categoryIds) {
                    $q->whereIn('term_taxonomy_id', $categoryIds);
                })
                ->limit(4)
                ->get()
                ->map(function($p) {
                    $m = is_string($p->meta_data) ? json_decode($p->meta_data, true) : ($p->meta_data ?? []);
                    $p->name = $p->title;
                    $p->price = $m['price'] ?? 0;
                    $p->sale_price = $m['sale_price'] ?? 0;
                    return $p;
                });
        }
        
        // Get product reviews (assuming relationship exists on Post or we skip for now)
        $reviews = collect(); // Adjust if reviews table exists for posts
        
        return view('frontend.products.show', compact('product', 'relatedProducts', 'reviews'));
    }
}
