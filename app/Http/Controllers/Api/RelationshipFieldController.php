<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Post;
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
        $tenantId = $this->getTenantId();

        $items = match ($type) {
            'product' => $this->searchProducts($query, $limit, $tenantId),
            'post' => $this->searchPosts($query, $limit, 'post', $tenantId),
            'page' => $this->searchPosts($query, $limit, 'page', $tenantId),
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
        $tenantId = $this->getTenantId();

        if (empty($ids)) {
            return response()->json(['items' => []]);
        }

        $items = match ($type) {
            'product' => $this->getProductsByIds($ids, $tenantId),
            'post' => $this->getPostsByIds($ids, 'post', $tenantId),
            'page' => $this->getPostsByIds($ids, 'page', $tenantId),
            default => [],
        };

        return response()->json(['items' => $items]);
    }

    /**
     * Get tenant ID from session
     */
    protected function getTenantId(): ?int
    {
        $currentProject = session('current_project');
        if (\is_array($currentProject)) {
            return $currentProject['id'] ?? null;
        }
        return $currentProject->id ?? null;
    }

    protected function searchProducts(string $query, int $limit, ?int $tenantId): array
    {
        $products = Product::query()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->when($query, fn($q) => $q->where(function($sub) use ($query) {
                $sub->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            }))
            ->where('status', 'published')
            ->orderBy('name')
            ->limit($limit)
            ->get();

        return $products->map(fn($p) => [
            'id' => $p->id,
            'title' => $p->name,
            'type' => 'Sản phẩm',
            'image' => $p->featured_image ?? ($p->gallery[0] ?? null),
            'price' => $p->price,
            'sale_price' => $p->sale_price,
            'sku' => $p->sku,
            'stock' => $p->stock_quantity ?? 0,
            'url' => $p->slug ? url($p->slug) : null,
        ])->toArray();
    }

    protected function searchPosts(string $query, int $limit, string $postType, ?int $tenantId): array
    {
        $posts = Post::query()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->when($query, fn($q) => $q->where('title', 'like', "%{$query}%"))
            ->where('type', $postType)
            ->where('status', 'published')
            ->orderBy('title')
            ->limit($limit)
            ->get();

        return $posts->map(fn($p) => [
            'id' => $p->id,
            'title' => $p->title,
            'type' => $postType === 'post' ? 'Bài viết' : 'Trang',
            'image' => $p->thumbnail,
            'excerpt' => \Str::limit(strip_tags($p->excerpt ?? $p->content ?? ''), 80),
            'url' => $p->slug ? url($p->slug) : null,
        ])->toArray();
    }

    protected function getProductsByIds(array $ids, ?int $tenantId): array
    {
        $products = Product::query()
            ->whereIn('id', $ids)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->get();

        return $products->map(fn($p) => [
            'id' => $p->id,
            'title' => $p->name,
            'type' => 'Sản phẩm',
            'image' => $p->featured_image ?? ($p->gallery[0] ?? null),
            'price' => $p->price,
            'sale_price' => $p->sale_price,
            'sku' => $p->sku,
            'stock' => $p->stock_quantity ?? 0,
            'url' => $p->slug ? url($p->slug) : null,
        ])->toArray();
    }

    protected function getPostsByIds(array $ids, string $postType, ?int $tenantId): array
    {
        $posts = Post::query()
            ->whereIn('id', $ids)
            ->where('type', $postType)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->get();

        return $posts->map(fn($p) => [
            'id' => $p->id,
            'title' => $p->title,
            'type' => $postType === 'post' ? 'Bài viết' : 'Trang',
            'image' => $p->thumbnail,
            'excerpt' => \Str::limit(strip_tags($p->excerpt ?? $p->content ?? ''), 80),
            'url' => $p->slug ? url($p->slug) : null,
        ])->toArray();
    }
}
