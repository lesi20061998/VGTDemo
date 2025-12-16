<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Post;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $menus = Menu::with(['items' => function($query) {
            $query->whereNull('parent_id')->with('children')->orderBy('order');
        }])->get();
        
        $selectedMenu = $menus->first();
        $pages = Post::where('post_type', 'page')->select('id', 'title')->get();
        $productCategories = ProductCategory::select('id', 'name', 'parent_id')->whereNull('parent_id')->with('children')->get();
        
        return view('cms.menus.index', compact('menus', 'selectedMenu', 'pages', 'productCategories'));
    }
    
    public function show($projectCode = null, $id = null)
    {
        // Nếu chỉ có 1 tham số, đó là ID
        if ($id === null) {
            $id = $projectCode;
        }
        
        $menu = Menu::findOrFail($id);
        $menus = Menu::with(['items' => function($query) {
            $query->whereNull('parent_id')->with('children')->orderBy('order');
        }])->get();
        
        $menu->load(['items' => function($query) {
            $query->whereNull('parent_id')->with('children')->orderBy('order');
        }]);
        $pages = Post::where('post_type', 'page')->select('id', 'title')->get();
        $productCategories = ProductCategory::select('id', 'name', 'parent_id')->whereNull('parent_id')->with('children')->get();
        
        return view('cms.menus.index', [
            'menus' => $menus,
            'selectedMenu' => $menu,
            'pages' => $pages,
            'productCategories' => $productCategories
        ]);
    }
    
    public function store(Request $request)
    {
        try {
            $tenantId = 1;
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255'
            ]);
            
            $exists = Menu::where('slug', $validated['slug'])->exists();
                
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Slug đã tồn tại. Vui lòng chọn tên khác.'
                ], 422);
            }
            
            $menu = Menu::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'location' => 'header',
                'is_active' => true,
                'tenant_id' => $tenantId
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Menu đã được tạo!',
                'menu' => $menu
            ]);
        } catch (\Exception $e) {
            \Log::error('Menu creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function storeItem(Request $request, $projectCode = null, $menuId = null)
    {
        if ($menuId === null) {
            $menuId = $projectCode;
        }
        
        $menu = Menu::findOrFail($menuId);
        
        try {
            $data = $request->validate([
                'title' => 'required|string',
                'url' => 'nullable|string',
                'target' => 'required|in:_self,_blank',
                'linkable_type' => 'nullable|string',
                'linkable_id' => 'nullable|integer',
                'parent_id' => 'nullable|exists:menu_items,id'
            ]);
            
            $data['menu_id'] = $menu->id;
            $data['order'] = MenuItem::where('menu_id', $menu->id)
                ->where(function($query) use ($data) {
                    if (isset($data['parent_id'])) {
                        $query->where('parent_id', $data['parent_id']);
                    } else {
                        $query->whereNull('parent_id');
                    }
                })
                ->max('order') + 1;
            
            $menuItem = MenuItem::create($data);
            
            return response()->json([
                'success' => true, 
                'message' => 'Mục menu đã được thêm!',
                'item' => $menuItem
            ]);
        } catch (\Exception $e) {
            \Log::error('Menu item creation failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function updateItem(Request $request, $projectCode = null, $itemId = null)
    {
        if ($itemId === null) {
            $itemId = $projectCode;
        }
        
        $item = MenuItem::findOrFail($itemId);
        $data = $request->validate([
            'title' => 'required|string',
            'url' => 'nullable|string',
            'target' => 'required|in:_self,_blank'
        ]);
        
        $item->update($data);
        
        return back()->with('success', 'Đã cập nhật!');
    }
    
    public function destroyItem($projectCode = null, $itemId = null)
    {
        if ($itemId === null) {
            $itemId = $projectCode;
        }
        
        $item = MenuItem::findOrFail($itemId);
        $item->delete();
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa mục menu!'
            ]);
        }
        
        return back()->with('success', 'Đã xóa mục menu!');
    }
    
    public function updateTree(Request $request, $projectCode = null, $menuId = null)
    {
        if ($menuId === null) {
            $menuId = $projectCode;
        }
        
        $menu = Menu::findOrFail($menuId);
        
        try {
            $tree = $request->input('tree', []);
            
            // Flatten the tree structure for database update
            $flatItems = $this->flattenTree($tree);
            
            // Update all items in a transaction
            \DB::transaction(function() use ($flatItems) {
                foreach ($flatItems as $item) {
                    MenuItem::where('id', $item['id'])->update([
                        'parent_id' => $item['parent_id'],
                        'order' => $item['order']
                    ]);
                }
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Cấu trúc menu đã được cập nhật!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Menu tree update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi cập nhật cấu trúc menu: ' . $e->getMessage()
            ], 500);
        }
    }
    

    
    private function flattenTree($items, $parentId = null, &$result = [])
    {
        foreach ($items as $index => $item) {
            $result[] = [
                'id' => $item['id'],
                'parent_id' => $parentId,
                'order' => $index,
                'depth' => $item['depth'] ?? 0
            ];
            
            if (!empty($item['children'])) {
                $this->flattenTree($item['children'], $item['id'], $result);
            }
        }
        
        return $result;
    }
    
    public function destroy($projectCode = null, $menuId = null)
    {
        if ($menuId === null) {
            $menuId = $projectCode;
        }
        
        $menu = Menu::findOrFail($menuId);
        $menuName = $menu->name;
        $menu->delete(); // Cascade sẽ tự động xóa tất cả menu_items
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã xóa menu '{$menuName}' và tất cả mục con!"
            ]);
        }
        
        return redirect()->route('cms.menus.index')->with('success', "Đã xóa menu '{$menuName}' và tất cả mục con!");
    }
}
