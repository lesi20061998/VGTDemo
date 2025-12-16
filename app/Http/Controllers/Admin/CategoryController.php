<?php
// MODIFIED: 2025-01-21

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Http\Requests\CategoryRequest;
use App\Traits\HasAlerts;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use HasAlerts;
    public function index(Request $request)
    {
        $categories = ProductCategory::with(['parent', 'children'])
            ->when($request->search, fn($q) => $q->search($request->search))
            ->when($request->parent_id, fn($q) => $q->where('parent_id', $request->parent_id))
            ->orderBy('path')
            ->orderBy('sort_order')
            ->get();

        $parentCategories = ProductCategory::whereNull('parent_id')->active()->get();

        return view('cms.categories.index', compact('categories', 'parentCategories'));
    }

    public function create()
    {
        $parentCategories = ProductCategory::active()->get();
        return view('cms.categories.create', compact('parentCategories'));
    }

    public function store(CategoryRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        
        // Calculate level and path
        if ($data['parent_id']) {
            $parent = ProductCategory::find($data['parent_id']);
            $data['level'] = $parent->level + 1;
            $data['path'] = $parent->path . '/' . $data['slug'];
        } else {
            $data['level'] = 0;
            $data['path'] = $data['slug'];
        }

        ProductCategory::create($data);

        return redirect()->route('cms.categories.index')->with('alert', [
            'type' => 'success',
            'message' => 'Thêm danh mục thành công!'
        ]);
    }

    public function show(ProductCategory $category)
    {
        $category->load(['parent', 'children', 'products']);
        return view('cms.categories.show', compact('category'));
    }

    public function edit(ProductCategory $category)
    {
        $parentCategories = ProductCategory::where('id', '!=', $category->id)
                                         ->active()
                                         ->get();
        
        return view('cms.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(CategoryRequest $request, ProductCategory $category)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        
        // Recalculate level and path
        if ($data['parent_id']) {
            $parent = ProductCategory::find($data['parent_id']);
            $data['level'] = $parent->level + 1;
            $data['path'] = $parent->path . '/' . $data['slug'];
        } else {
            $data['level'] = 0;
            $data['path'] = $data['slug'];
        }

        $category->update($data);

        return redirect()->route('cms.categories.index')->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật danh mục thành công!'
        ]);
    }

    public function destroy(ProductCategory $category)
    {
        if ($category->children()->count() > 0) {
            return redirect()->back()->with('alert', [
                'type' => 'error',
                'message' => 'Không thể xóa danh mục có danh mục con!'
            ]);
        }

        if ($category->products()->count() > 0) {
            return redirect()->back()->with('alert', [
                'type' => 'error',
                'message' => 'Không thể xóa danh mục có sản phẩm!'
            ]);
        }

        $category->delete();
        
        return redirect()->route('cms.categories.index')->with('alert', [
            'type' => 'success',
            'message' => 'Xóa danh mục thành công!'
        ]);
    }

    public function getSubcategories(Request $request)
    {
        $subcategories = ProductCategory::where('parent_id', $request->parent_id)
                                      ->active()
                                      ->orderBy('sort_order')
                                      ->get();
        
        return response()->json($subcategories);
    }
}
