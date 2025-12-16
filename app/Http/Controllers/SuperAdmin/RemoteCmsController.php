<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Post;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class RemoteCmsController extends Controller
{
    private function switchToProjectDatabase($projectCode)
    {
        $projectDbName = 'project_' . strtolower($projectCode);
        
        Config::set('database.connections.project', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $projectDbName,
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
        ]);
        
        DB::purge('project');
        DB::setDefaultConnection('project');
    }
    
    public function menus($projectCode)
    {
        $this->switchToProjectDatabase($projectCode);
        
        $menus = Menu::with(['items' => function($query) {
            $query->whereNull('parent_id')->with('children')->orderBy('order');
        }])->get();
        
        $selectedMenu = $menus->first();
        $pages = Post::where('post_type', 'page')->select('id', 'title')->get();
        $productCategories = ProductCategory::select('id', 'name', 'parent_id')->whereNull('parent_id')->with('children')->get();
        
        return view('cms.menus.index', compact('menus', 'selectedMenu', 'pages', 'productCategories', 'projectCode'));
    }
    
    public function storeMenu(Request $request, $projectCode)
    {
        $this->switchToProjectDatabase($projectCode);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255'
        ]);
        
        $menu = Menu::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'location' => 'header',
            'is_active' => true,
            'tenant_id' => 1
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Menu đã được tạo!',
            'menu' => $menu
        ]);
    }
    
    public function showMenu($projectCode, $menu)
    {
        $this->switchToProjectDatabase($projectCode);
        
        $menu = Menu::findOrFail($menu);
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
            'productCategories' => $productCategories,
            'projectCode' => $projectCode
        ]);
    }
    
    public function destroyMenu($projectCode, $menu)
    {
        $this->switchToProjectDatabase($projectCode);
        
        $menu = Menu::findOrFail($menu);
        $menuName = $menu->name;
        $menu->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Đã xóa menu '{$menuName}'!"
        ]);
    }
    
    public function storeMenuItem(Request $request, $projectCode, $menu)
    {
        $this->switchToProjectDatabase($projectCode);
        
        $menu = Menu::findOrFail($menu);
        
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
    }
    
    public function destroyMenuItem($projectCode, $item)
    {
        $this->switchToProjectDatabase($projectCode);
        
        $item = MenuItem::findOrFail($item);
        $item->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Đã xóa mục menu!'
        ]);
    }
    
    public function updateMenuTree(Request $request, $projectCode, $menu)
    {
        $this->switchToProjectDatabase($projectCode);
        
        $tree = $request->input('tree', []);
        
        DB::transaction(function() use ($tree) {
            foreach ($tree as $item) {
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
    }
}
