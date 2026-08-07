<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ProjectProduct;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display product listing page
     */
    public function index(Request $request)
    {
        $query = ProjectProduct::where('status', 'published');
        
        // Filter by category
        if ($request->has('category')) {
            $category = ProductCategory::where('slug', $request->category)->first();
            if ($category) {
                $query->where('product_category_id', $category->id);
            }
        }
        
        // Filter by brand
        if ($request->has('brand')) {
            $query->where('brand_id', $request->brand);
        }
        
        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // Search
        if ($request->has('q') && $request->q) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->q}%")
                  ->orWhere('description', 'like', "%{$request->q}%");
            });
        }
        
        // Sort
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'popular':
                $query->orderBy('views', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
        
        $products = $query->paginate(12);
        $categories = ProductCategory::where('is_active', true)->orderBy('order')->get();
        
        return view('frontend.products.index', compact('products', 'categories'));
    }
    
    /**
     * Display products by category
     */
    public function category($categorySlug)
    {
        $category = ProductCategory::where('slug', $categorySlug)->firstOrFail();
        
        $products = ProjectProduct::where('status', 'published')
            ->where('product_category_id', $category->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
        $categories = ProductCategory::where('is_active', true)->orderBy('order')->get();
        
        return view('frontend.products.index', compact('products', 'categories', 'category'));
    }
    
    /**
     * Display single product detail
     */
    public function show($projectCode, $slug)
    {
        $product = ProjectProduct::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
        
        // Increment views
        $product->increment('views');
        
        // Get related products
        $relatedProducts = ProjectProduct::where('status', 'published')
            ->where('id', '!=', $product->id)
            ->where('product_category_id', $product->product_category_id)
            ->limit(4)
            ->get();
        
        // Get product reviews
        $reviews = $product->reviews()->where('status', 'approved')->latest()->get();
        
        return view('frontend.products.show', compact('product', 'relatedProducts', 'reviews'));
    }
}
