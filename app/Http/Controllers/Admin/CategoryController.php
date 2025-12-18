<?php

// MODIFIED: 2025-01-21

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\ProductCategory;
use App\Models\ProjectProductCategory;
use App\Traits\HasAlerts;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use HasAlerts;

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
        $parentCategories = $modelClass::active()->get();

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

        // Calculate level and path
        if (!empty($data['parent_id'])) {
            $parent = $modelClass::find($data['parent_id']);
            $data['level'] = $parent->level + 1;
            $data['path'] = $parent->path.'/'.$data['slug'];
        } else {
            $data['level'] = 0;
            $data['path'] = $data['slug'];
        }

        // Set default sort_order if not provided
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }

        $modelClass::create($data);

        $projectCode = request()->route('projectCode');
        $route = $projectCode
            ? route('project.admin.categories.index', $projectCode)
            : route('cms.categories.index');

        return redirect($route)->with('alert', [
            'type' => 'success',
            'message' => 'Thêm danh mục thành công!',
        ]);
    }

    // public function show($id)
    // {
    //     $modelClass = $this->getCategoryModel();
    //     $category = $modelClass::with(['parent', 'children'])->findOrFail($id);
       
    //     return view('cms.categories.show', compact('category'));
    // }

    public function edit($id)
    {
        $modelClass = $this->getCategoryModel();
        $category = $modelClass::with(['parent', 'children'])->findOrFail($id);
        dd($modelClass);
        die();
       
       
        //return view('cms.categories.edit', compact('category', 'parentCategories'));
    }

    // public function update(CategoryRequest $request, $id)
    // {
    //     $modelClass = $this->getCategoryModel();
    //     $category = $modelClass::findOrFail($id);

    //     $data = $request->validated();

    //     // Generate unique slug (excluding current category)
    //     if (empty($data['slug'])) {
    //         $data['slug'] = $this->generateUniqueSlug($data['name'], $category->id);
    //     } else {
    //         // If slug is provided, still check for uniqueness
    //         $data['slug'] = $this->generateUniqueSlug($data['slug'], $category->id);
    //     }

    //     // Recalculate level and path
    //     if (!empty($data['parent_id'])) {
    //         $parent = $modelClass::find($data['parent_id']);
    //         $data['level'] = $parent->level + 1;
    //         $data['path'] = $parent->path.'/'.$data['slug'];
    //     } else {
    //         $data['level'] = 0;
    //         $data['path'] = $data['slug'];
    //     }

    //     $category->update($data);

    //     $projectCode = request()->route('projectCode');
    //     $route = $projectCode
    //         ? route('project.admin.categories.index', $projectCode)
    //         : route('cms.categories.index');

    //     return redirect($route)->with('alert', [
    //         'type' => 'success',
    //         'message' => 'Cập nhật danh mục thành công!',
    //     ]);
    // }

    // public function destroy($id)
    // {
    //     $modelClass = $this->getCategoryModel();
    //     $category = $modelClass::findOrFail($id);

    //     if ($category->children()->count() > 0) {
    //         return redirect()->back()->with('alert', [
    //             'type' => 'error',
    //             'message' => 'Không thể xóa danh mục có danh mục con!',
    //         ]);
    //     }

    //     if ($category->products()->count() > 0) {
    //         return redirect()->back()->with('alert', [
    //             'type' => 'error',
    //             'message' => 'Không thể xóa danh mục có sản phẩm!',
    //         ]);
    //     }

    //     $category->delete();

    //     $projectCode = request()->route('projectCode');
    //     $route = $projectCode
    //         ? route('project.admin.categories.index', $projectCode)
    //         : route('cms.categories.index');

    //     return redirect($route)->with('alert', [
    //         'type' => 'success',
    //         'message' => 'Xóa danh mục thành công!',
    //     ]);
    // }

    // public function getSubcategories(Request $request)
    // {
    //     $modelClass = $this->getCategoryModel();
    //     $subcategories = $modelClass::where('parent_id', $request->parent_id)
    //         ->active()
    //         ->orderBy('sort_order')
    //         ->get();

    //     return response()->json($subcategories);
    // }
}
