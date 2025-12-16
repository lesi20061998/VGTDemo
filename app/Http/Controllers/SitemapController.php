<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Brand;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemaps = [
            ['loc' => route('sitemap.pages'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('sitemap.products'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('sitemap.categories'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('sitemap.brands'), 'lastmod' => now()->toAtomString()],
        ];

        return response()->view('sitemap.index', compact('sitemaps'))
            ->header('Content-Type', 'application/xml');
    }

    public function pages()
    {
        $urls = [
            ['loc' => url('/'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => route('frontend.contact'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.8'],
        ];

        return response()->view('sitemap.urlset', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }

    public function products()
    {
        $products = Product::where('status', 'active')
            ->select('id', 'slug', 'updated_at')
            ->get();

        $urls = $products->map(function ($product) {
            return [
                'loc' => route('frontend.products.show', $product->slug),
                'lastmod' => $product->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.9'
            ];
        });

        return response()->view('sitemap.urlset', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }

    public function categories()
    {
        $categories = ProductCategory::select('id', 'slug', 'updated_at')->get();

        $urls = $categories->map(function ($category) {
            return [
                'loc' => route('frontend.categories.show', $category->slug),
                'lastmod' => $category->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ];
        });

        return response()->view('sitemap.urlset', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }

    public function brands()
    {
        $brands = Brand::select('id', 'slug', 'updated_at')->get();

        $urls = $brands->map(function ($brand) {
            return [
                'loc' => url('/brand/' . $brand->slug),
                'lastmod' => $brand->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ];
        });

        return response()->view('sitemap.urlset', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }
}

