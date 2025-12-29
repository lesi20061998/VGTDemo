<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Post;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ArchiveTemplate;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    /**
     * Product archive page
     */
    public function products(Request $request)
    {
        $query = Product::query()->where('status', 'published');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('categories')) {
            $categoryIds = explode(',', $request->categories);
            $query->whereIn('category_id', $categoryIds);
        }

        // Filter by brand
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // Filter by price range
        if ($request->filled('price')) {
            [$min, $max] = $this->parsePriceRange($request->price);
            if ($min !== null) {
                $query->where('price', '>=', $min);
            }
            if ($max !== null) {
                $query->where('price', '<=', $max);
            }
        }

        // Search
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        // Sort
        $query = $this->applySorting($query, $request->get('sort', 'newest'));

        $products = $query->paginate($request->get('per_page', 12));

        // Get filter data
        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('sort_order')
            ->get();

        $brands = Brand::where('is_active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        // Check for custom archive template
        $template = ArchiveTemplate::getDefault('product');
        
        if ($template) {
            return response($template->render([
                'products' => $products,
                'categories' => $categories,
                'brands' => $brands,
                'title' => 'Sản phẩm',
            ]));
        }

        return view('frontend.archives.product', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'title' => 'Tất cả sản phẩm',
        ]);
    }

    /**
     * Category archive page
     */
    public function category(Request $request, string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $query = Product::query()
            ->where('status', 'published')
            ->where('category_id', $category->id);

        // Apply sorting
        $query = $this->applySorting($query, $request->get('sort', 'newest'));

        $products = $query->paginate($request->get('per_page', 12));

        // Get subcategories
        $subcategories = Category::where('parent_id', $category->id)
            ->where('is_active', true)
            ->withCount('products')
            ->get();

        // Get brands in this category
        $brands = Brand::whereHas('products', function ($q) use ($category) {
            $q->where('category_id', $category->id);
        })->withCount('products')->get();

        return view('frontend.archives.product', [
            'products' => $products,
            'category' => $category,
            'categories' => $subcategories,
            'brands' => $brands,
            'title' => $category->name,
        ]);
    }

    /**
     * Posts/Blog archive page
     */
    public function posts(Request $request, ?string $type = 'post')
    {
        $query = Post::query()
            ->where('status', 'published')
            ->where('post_type', $type);

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('content', 'like', '%' . $request->q . '%');
            });
        }

        $query->orderBy('created_at', 'desc');

        $posts = $query->paginate($request->get('per_page', 9));

        // Get featured post
        $featuredPost = Post::where('status', 'published')
            ->where('post_type', $type)
            ->where('is_featured', true)
            ->latest()
            ->first();

        // Get categories
        $categories = \App\Models\PostCategory::withCount('posts')
            ->orderBy('name')
            ->get();

        // Check for custom archive template
        $template = ArchiveTemplate::getDefault('post');
        
        if ($template) {
            return response($template->render([
                'posts' => $posts,
                'categories' => $categories,
                'featuredPost' => $featuredPost,
                'title' => $this->getPostTypeTitle($type),
            ]));
        }

        return view('frontend.archives.post', [
            'posts' => $posts,
            'categories' => $categories,
            'featuredPost' => $featuredPost,
            'title' => $this->getPostTypeTitle($type),
            'postType' => $type,
        ]);
    }

    /**
     * Apply sorting to query
     */
    protected function applySorting($query, string $sort)
    {
        return match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'bestseller' => $query->orderBy('sold_count', 'desc'),
            'rating' => $query->orderBy('rating', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };
    }

    /**
     * Parse price range string
     */
    protected function parsePriceRange(string $range): array
    {
        if (str_ends_with($range, '+')) {
            return [(int) str_replace('+', '', $range), null];
        }

        $parts = explode('-', $range);
        return [
            (int) ($parts[0] ?? 0),
            isset($parts[1]) ? (int) $parts[1] : null,
        ];
    }

    /**
     * Get post type title
     */
    protected function getPostTypeTitle(string $type): string
    {
        return match ($type) {
            'post' => 'Tin tức & Bài viết',
            'news' => 'Tin tức',
            'blog' => 'Blog',
            'page' => 'Trang',
            default => ucfirst($type),
        };
    }
}
