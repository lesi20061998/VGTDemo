<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Post;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class RelationshipFieldController extends Controller
{
    /**
     * Search items for relationship field
     */
    public function search(Request $request)
    {
        $type = $request->get('type', 'product');
        $query = $request->get('q', '');
        $limit = $request->get('limit', 20);

        $items = match ($type) {
            'product' => $this->searchProducts($query, $limit),
            'post' => $this->searchPosts($query, $limit, 'post'),
            'page' => $this->searchPosts($query, $limit, 'page'),
            default => [],
        };

        return response()->json(['items' => $items]);
    }

    /**
     * Get items by IDs
     */
    public function getItems(Request $request)
    {
        $type = $request->get('type', 'product');
        $ids = array_filter(explode(',', $request->get('ids', '')));

        if (empty($ids)) {
            return response()->json(['items' => []]);
        }

        $items = match ($type) {
            'product' => $this->getProductsByIds($ids),
            'post' => $this->getPostsByIds($ids, 'post'),
            'page' => $this->getPostsByIds($ids, 'page'),
            default => [],
        };

        return response()->json(['items' => $items]);
    }

    protected function searchProducts(string $query, int $limit): array
    {
        $products = Product::query()
            ->when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))
            ->where('status', 'published')
            ->limit($limit)
            ->get();

        return $products->map(fn($p) => [
            'id' => $p->id,
            'title' => $p->name,
            'type' => 'Sản phẩm',
            'image' => $p->featured_image ?? ($p->gallery[0] ?? null),
            'price' => $p->price,
            'sku' => $p->sku,
        ])->toArray();
    }

    protected function searchPosts(string $query, int $limit, string $postType): array
    {
        $posts = Post::query()
            ->when($query, fn($q) => $q->where('title', 'like', "%{$query}%"))
            ->where('type', $postType)
            ->where('status', 'published')
            ->limit($limit)
            ->get();

        return $posts->map(fn($p) => [
            'id' => $p->id,
            'title' => $p->title,
            'type' => $postType === 'post' ? 'Bài viết' : 'Trang',
            'image' => $p->thumbnail,
        ])->toArray();
    }

    protected function getProductsByIds(array $ids): array
    {
        $products = Product::whereIn('id', $ids)->get();

        return $products->map(fn($p) => [
            'id' => $p->id,
            'title' => $p->name,
            'type' => 'Sản phẩm',
            'image' => $p->featured_image ?? ($p->gallery[0] ?? null),
            'price' => $p->price,
            'sku' => $p->sku,
        ])->toArray();
    }

    protected function getPostsByIds(array $ids, string $postType): array
    {
        $posts = Post::whereIn('id', $ids)->where('type', $postType)->get();

        return $posts->map(fn($p) => [
            'id' => $p->id,
            'title' => $p->title,
            'type' => $postType === 'post' ? 'Bài viết' : 'Trang',
            'image' => $p->thumbnail,
        ])->toArray();
    }
}
