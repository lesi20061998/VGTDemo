<?php

// MODIFIED: 2026-08-07

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Taxonomy;
use App\Traits\HasCrudAlerts;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use HasCrudAlerts;

    const MAX_DEPTH = 3;

    private function getCategoryQuery()
    {
        return Taxonomy::where('taxonomy', 'product_cat');
    }

    private function generateUniqueSlug($name, $excludeId = null)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $query = Taxonomy::where('taxonomy', 'product_cat')->where('slug', $slug);

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (! $query->exists()) {
                break;
            }

            $counter++;
            $slug = $baseSlug.'-'.$counter;
        }

        return $slug;
    }

    public function index(Request $request)
    {
        $categories = $this->getCategoryQuery()
            ->with(['parent', 'children'])
            ->withCount('posts as products_count')
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->parent_id, fn ($q) => $q->where('parent_id', $request->parent_id))
            ->orderBy('order')
            ->get();

        $parentCategories = $this->getCategoryQuery()->whereNull('parent_id')->get();

        return view('cms.categories.index', compact('categories', 'parentCategories'));
    }

    public function create()
    {
        $parentCategories = $this->getCategoryQuery()->get();

        return view('cms.categories.create', compact('parentCategories'));
    }

    public function store(CategoryRequest $request)
    {
        $data = $request->validated();

        $slug = empty($data['slug']) ? $this->generateUniqueSlug($data['name']) : $this->generateUniqueSlug($data['slug']);

        $metaData = [];

        if ($request->has('image') && ! empty($request->input('image'))) {
            $metaData['image'] = $request->input('image');
        }

        $level = 0;
        $path = $slug;

        if (! empty($data['parent_id'])) {
            $parent = $this->getCategoryQuery()->find($data['parent_id']);
            if (! $parent) {
                return back()->withInput()->with('alert', ['type' => 'error', 'message' => 'Danh mục cha không tồn tại!']);
            }

            $parentMeta = is_string($parent->meta_data) ? json_decode($parent->meta_data, true) : ($parent->meta_data ?? []);
            $parentLevel = $parentMeta['level'] ?? 0;
            $parentPath = $parentMeta['path'] ?? $parent->slug;

            $level = $parentLevel + 1;

            if ($level > self::MAX_DEPTH) {
                return back()->withInput()->with('alert', ['type' => 'error', 'message' => 'Không thể tạo danh mục quá '.(self::MAX_DEPTH + 1).' cấp độ!']);
            }

            $path = $parentPath.'/'.$slug;
        }

        $metaData['level'] = $level;
        $metaData['path'] = $path;
        $metaData['is_active'] = $request->has('is_active') ? true : false;

        $category = Taxonomy::create([
            'name' => $data['name'],
            'slug' => $slug,
            'taxonomy' => 'product_cat',
            'description' => $data['description'] ?? '',
            'parent_id' => $data['parent_id'] ?? null,
            'order' => $data['sort_order'] ?? 0,
            'status' => $request->has('is_active') ? 'published' : 'draft',
            'meta_data' => $metaData,
        ]);

        $this->alertCreated('danh mục', "Danh mục '{$category->name}' đã được thêm vào hệ thống.");

        $projectCode = request()->route('projectCode');
        $route = $projectCode ? route('project.admin.categories.index', $projectCode) : route('cms.categories.index');

        return redirect($route);
    }

    public function show($projectCodeOrId, $categoryId = null)
    {
        $id = $categoryId ?? $projectCodeOrId;
        $category = $this->getCategoryQuery()->with(['parent', 'children'])->findOrFail($id);

        return view('cms.categories.show', compact('category'));
    }

    public function edit($projectCodeOrId, $categoryId = null)
    {
        $id = $categoryId ?? $projectCodeOrId;
        $category = $this->getCategoryQuery()->findOrFail($id);

        $parentCategories = $this->getCategoryQuery()->where('id', '!=', $category->id)->get();

        return view('cms.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(CategoryRequest $request, $projectCodeOrId, $categoryId = null)
    {
        $id = $categoryId ?? $projectCodeOrId;
        $category = $this->getCategoryQuery()->findOrFail($id);
        $data = $request->validated();

        $slug = empty($data['slug']) ? $this->generateUniqueSlug($data['name'], $category->id) : $this->generateUniqueSlug($data['slug'], $category->id);

        $metaData = is_string($category->meta_data) ? json_decode($category->meta_data, true) : ($category->meta_data ?? []);

        if ($request->has('image')) {
            $imageValue = $request->input('image');
            if (! empty($imageValue) && $imageValue !== 'null' && $imageValue !== '') {
                $metaData['image'] = $imageValue;
            } else {
                unset($metaData['image']);
            }
        }

        $level = 0;
        $path = $slug;

        if (! empty($data['parent_id'])) {
            $parent = $this->getCategoryQuery()->find($data['parent_id']);
            if (! $parent) {
                return back()->withInput()->with('alert', ['type' => 'error', 'message' => 'Danh mục cha không tồn tại!']);
            }

            $parentMeta = is_string($parent->meta_data) ? json_decode($parent->meta_data, true) : ($parent->meta_data ?? []);
            $parentLevel = $parentMeta['level'] ?? 0;
            $parentPath = $parentMeta['path'] ?? $parent->slug;

            $level = $parentLevel + 1;

            if ($level > self::MAX_DEPTH) {
                return back()->withInput()->with('alert', ['type' => 'error', 'message' => 'Không thể chuyển danh mục này! Hệ thống chỉ hỗ trợ tối đa '.(self::MAX_DEPTH + 1).' cấp.']);
            }

            $path = $parentPath.'/'.$slug;
        }

        $metaData['level'] = $level;
        $metaData['path'] = $path;
        $metaData['is_active'] = $request->has('is_active') ? true : false;

        $category->update([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? $category->description,
            'parent_id' => $data['parent_id'] ?? null,
            'order' => $data['sort_order'] ?? $category->order,
            'status' => $request->has('is_active') ? 'published' : 'draft',
            'meta_data' => $metaData,
        ]);

        $this->alertUpdated('danh mục', "Danh mục '{$category->name}' đã được cập nhật.");

        $projectCode = request()->route('projectCode');
        $route = $projectCode ? route('project.admin.categories.index', $projectCode) : route('cms.categories.index');

        return redirect($route);
    }

    public function destroy($projectCodeOrId, $categoryId = null)
    {
        $id = $categoryId ?? $projectCodeOrId;
        $category = $this->getCategoryQuery()->findOrFail($id);

        if ($category->posts()->count() > 0) {
            return back()->with('alert', ['type' => 'error', 'message' => 'Không thể xóa danh mục có sản phẩm!']);
        }

        $categoryName = $category->name;
        $category->delete();

        $this->alertDeleted('danh mục', "Danh mục '{$categoryName}' đã được xóa.");

        $projectCode = request()->route('projectCode');
        $route = $projectCode ? route('project.admin.categories.index', $projectCode) : route('cms.categories.index');

        return redirect($route);
    }

    public function getSubcategories(Request $request)
    {
        $subcategories = $this->getCategoryQuery()->where('parent_id', $request->parent_id)
            ->orderBy('order')
            ->get();

        return response()->json($subcategories);
    }
}
