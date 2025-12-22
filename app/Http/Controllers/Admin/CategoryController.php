<?php

// MODIFIED: 2025-01-21

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\ProductCategory;
use App\Models\ProjectProductCategory;
use App\Traits\HasCrudAlerts;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use HasCrudAlerts;

    /**
     * Get the appropriate model based on context (project vs cms)
     */
    private function getCategoryModel()
    {
        // Check if we're in project context
        $projectCode = request()->route('projectCode');

        if ($projectCode) {
            return ProjectProductCategory::class;
        }

        return ProductCategory::class;
    }

    /**
     * Get the appropriate route name based on context
     */
    private function getRoutePrefix()
    {
        $projectCode = request()->route('projectCode');

        if ($projectCode) {
            return 'project.admin';
        }

        return 'cms';
    }

    /**
     * Generate unique slug for category
     */
    private function generateUniqueSlug($name, $excludeId = null)
    {
        $modelClass = $this->getCategoryModel();
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $query = $modelClass::where('slug', $slug);

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
        $modelClass = $this->getCategoryModel();

        $categories = $modelClass::with(['parent', 'children'])
            ->withCount('products')
            ->when($request->search, fn ($q) => $q->search($request->search))
            ->when($request->parent_id, fn ($q) => $q->where('parent_id', $request->parent_id))
            ->orderBy('path')
            ->orderBy('sort_order')
            ->get();

        $parentCategories = $modelClass::whereNull('parent_id')->active()->get();

        return view('cms.categories.index', compact('categories', 'parentCategories'));
    }

    public function create()
    {
        $modelClass = $this->getCategoryModel();
        $parentCategories = $modelClass::where('level', '<', $modelClass::MAX_DEPTH)
            ->active()
            ->get();

        return view('cms.categories.create', compact('parentCategories'));
    }

    public function store(CategoryRequest $request)
    {
        $modelClass = $this->getCategoryModel();

        $data = $request->validated();

        // Generate unique slug
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name']);
        } else {
            // If slug is provided, still check for uniqueness
            $data['slug'] = $this->generateUniqueSlug($data['slug']);
        }

        // Handle image URL from media manager
        if ($request->has('image')) {
            $imageValue = $request->input('image');
            if (! empty($imageValue)) {
                $data['image'] = $imageValue;
            }
            // Nếu rỗng thì không set gì (để null mặc định)
        }

        // Calculate level and path
        if (! empty($data['parent_id'])) {
            $parent = $modelClass::find($data['parent_id']);
            if (! $parent) {
                return back()
                    ->withInput()
                    ->with('alert', [
                        'type' => 'error',
                        'message' => 'Danh mục cha không tồn tại!',
                    ]);
            }

            $newLevel = $parent->level + 1;

            // Check maximum depth limit (4 levels: 0,1,2,3)
            if ($newLevel > $modelClass::MAX_DEPTH) {
                return back()
                    ->withInput()
                    ->with('alert', [
                        'type' => 'error',
                        'message' => 'Không thể tạo danh mục quá '.($modelClass::MAX_DEPTH + 1).' cấp độ! Hệ thống chỉ hỗ trợ tối đa '.($modelClass::MAX_DEPTH + 1).' cấp (0, 1, 2, 3).',
                    ]);
            }

            $data['level'] = $newLevel;
            $data['path'] = $parent->path.'/'.$data['slug'];
        } else {
            $data['level'] = 0;
            $data['path'] = $data['slug'];
        }

        // Set default sort_order if not provided
        if (! isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }

        // Handle checkbox is_active (checkbox only sends value when checked)
        $data['is_active'] = $request->has('is_active') ? true : false;

        $category = $modelClass::create($data);

        $this->alertCreated('danh mục', "Danh mục '{$category->name}' đã được thêm vào hệ thống.");

        $projectCode = request()->route('projectCode');
        $route = $projectCode
            ? route('project.admin.categories.index', $projectCode)
            : route('cms.categories.index');

        return redirect($route);
    }

    public function show($projectCodeOrId, $categoryId = null)
    {
        // Determine if this is a project route or CMS route
        $id = $categoryId ?? $projectCodeOrId;

        $modelClass = $this->getCategoryModel();
        $category = $modelClass::with(['parent', 'children'])->findOrFail($id);

        return view('cms.categories.show', compact('category'));
    }

    public function edit($projectCodeOrId, $categoryId = null)
    {
        // Determine if this is a project route or CMS route
        $id = $categoryId ?? $projectCodeOrId;

        $modelClass = $this->getCategoryModel();
        $category = $modelClass::findOrFail($id);

        $parentCategories = $modelClass::where('id', '!=', $category->id)
            ->where('level', '<', $modelClass::MAX_DEPTH)
            ->active()
            ->get()
            ->filter(function ($potentialParent) use ($category) {
                // Exclude descendants to prevent circular references
                $descendants = $category->getDescendants();

                return ! $descendants->contains('id', $potentialParent->id);
            });

        return view('cms.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(CategoryRequest $request, $projectCodeOrId, $categoryId = null)
    {
        // Determine if this is a project route or CMS route
        $id = $categoryId ?? $projectCodeOrId;

        $modelClass = $this->getCategoryModel();
        $category = $modelClass::findOrFail($id);

        $data = $request->validated();

        // Store old level to check if it changed
        $oldLevel = $category->level;
        $oldParentId = $category->parent_id;

        // Generate unique slug (excluding current category)
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $category->id);
        } else {
            // If slug is provided, still check for uniqueness
            $data['slug'] = $this->generateUniqueSlug($data['slug'], $category->id);
        }

        // Handle image URL from media manager
        if ($request->has('image')) {
            $imageValue = $request->input('image');
            // Debug: Log giá trị image nhận được
            \Log::info('Category update - Image value received:', ['image' => $imageValue, 'empty' => empty($imageValue), 'is_null' => is_null($imageValue)]);

            if (! empty($imageValue) && $imageValue !== 'null' && $imageValue !== '') {
                // Có hình ảnh mới
                $data['image'] = $imageValue;
            } else {
                // Người dùng xóa hình ảnh (gửi giá trị rỗng hoặc null)
                $data['image'] = null;
            }
        }

        // Recalculate level and path to ensure consistency
        if (! empty($data['parent_id'])) {
            $parent = $modelClass::find($data['parent_id']);
            if (! $parent) {
                return back()
                    ->withInput()
                    ->with('alert', [
                        'type' => 'error',
                        'message' => 'Danh mục cha không tồn tại!',
                    ]);
            }

            $newLevel = $parent->level + 1;

            // Check maximum depth limit (4 levels: 0,1,2,3)
            if ($newLevel > $modelClass::MAX_DEPTH) {
                return back()
                    ->withInput()
                    ->with('alert', [
                        'type' => 'error',
                        'message' => 'Không thể chuyển danh mục này! Việc chuyển sẽ tạo ra cấu trúc quá '.($modelClass::MAX_DEPTH + 1).' cấp độ. Hệ thống chỉ hỗ trợ tối đa '.($modelClass::MAX_DEPTH + 1).' cấp (0, 1, 2, 3).',
                    ]);
            }

            // Check if moving this category would cause its children to exceed max depth
            if ($category->children()->count() > 0) {
                $maxChildDepth = $this->getMaxDescendantDepth($category);
                $depthIncrease = $newLevel - $category->level;

                if ($maxChildDepth + $depthIncrease > $modelClass::MAX_DEPTH) {
                    return back()
                        ->withInput()
                        ->with('alert', [
                            'type' => 'error',
                            'message' => 'Không thể chuyển danh mục này! Danh mục có các danh mục con, việc chuyển sẽ làm các danh mục con vượt quá '.($modelClass::MAX_DEPTH + 1).' cấp độ cho phép.',
                        ]);
                }
            }

            $data['level'] = $newLevel;
            $data['path'] = $parent->path.'/'.$data['slug'];
        } else {
            $data['level'] = 0;
            $data['path'] = $data['slug'];
        }

        // Handle checkbox is_active (checkbox only sends value when checked)
        $data['is_active'] = $request->has('is_active') ? true : false;

        $category->update($data);

        // Always ensure hierarchy consistency after update
        $category->fixHierarchyConsistency();

        $this->alertUpdated('danh mục', "Danh mục '{$category->name}' đã được cập nhật.");

        $projectCode = request()->route('projectCode');
        $route = $projectCode
            ? route('project.admin.categories.index', $projectCode)
            : route('cms.categories.index');

        return redirect($route);
    }

    public function destroy($projectCodeOrId, $categoryId = null)
    {
        // Determine if this is a project route or CMS route
        $id = $categoryId ?? $projectCodeOrId;

        $modelClass = $this->getCategoryModel();
        $category = $modelClass::findOrFail($id);

        // Check if category has products - still prevent deletion if has products
        if ($category->products()->count() > 0) {
            return back()->with('alert', [
                'type' => 'error',
                'message' => 'Không thể xóa danh mục có sản phẩm! Vui lòng di chuyển sản phẩm sang danh mục khác trước.',
            ]);
        }

        // Handle children: promote them to parent's level
        if ($category->children()->count() > 0) {
            $newParentId = $category->parent_id; // Children will inherit this category's parent
            $newLevel = $category->level; // Children will move to this category's level

            foreach ($category->children as $child) {
                // Update child's parent and level
                $child->parent_id = $newParentId;
                $child->level = $newLevel;

                // Recalculate path
                if ($newParentId) {
                    $newParent = $modelClass::find($newParentId);
                    $child->path = $newParent->path.'/'.$child->slug;
                } else {
                    $child->path = $child->slug;
                }

                $child->save();

                // Recursively update all descendants
                $child->updateDescendantsHierarchy();
            }
        }

        $categoryName = $category->name;
        $category->delete();

        $this->alertDeleted('danh mục', "Danh mục '{$categoryName}' đã được xóa khỏi hệ thống.");

        $projectCode = request()->route('projectCode');
        $route = $projectCode
            ? route('project.admin.categories.index', $projectCode)
            : route('cms.categories.index');

        return redirect($route);
    }

    public function getSubcategories(Request $request)
    {
        $modelClass = $this->getCategoryModel();
        $subcategories = $modelClass::where('parent_id', $request->parent_id)
            ->active()
            ->orderBy('sort_order')
            ->get();

        return response()->json($subcategories);
    }

    /**
     * Recursively update paths for all descendants of a category
     */
    private function updateDescendantsPaths($category, $modelClass)
    {
        foreach ($category->children as $child) {
            // Update child's path based on its parent's new path
            $child->path = $category->path.'/'.$child->slug;
            $child->save();

            // Recursively update this child's descendants
            $this->updateDescendantsPaths($child, $modelClass);
        }
    }

    /**
     * Get the maximum depth of all descendants of a category
     */
    private function getMaxDescendantDepth($category): int
    {
        $maxDepth = $category->level;

        foreach ($category->children as $child) {
            $childMaxDepth = $this->getMaxDescendantDepth($child);
            $maxDepth = max($maxDepth, $childMaxDepth);
        }

        return $maxDepth;
    }
}
