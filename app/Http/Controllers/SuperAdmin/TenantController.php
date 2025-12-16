<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::withCount(['users', 'products', 'posts', 'orders'])->get();
        
        return view('superadmin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('superadmin.tenants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:tenants',
            'domain' => 'required|string|max:255|unique:tenants',
            'database_name' => 'required|string|max:255',
            'create_website' => 'boolean',
            'export_path' => 'nullable|string'
        ]);

        $tenant = Tenant::create($request->except(['create_website', 'export_path']));

        if ($request->create_website) {
            \Artisan::call('website:create', [
                'tenant_code' => $tenant->code,
                '--export-path' => $request->export_path ?: "c:\\xampp\\htdocs\\{$tenant->code}"
            ]);
            
            return redirect()->route('superadmin.tenants.index')
                            ->with('success', 'Website đã được tạo và export thành công!');
        }

        return redirect()->route('superadmin.tenants.index')
                        ->with('success', 'Tenant đã được tạo thành công!');
    }

    public function show(Tenant $tenant)
    {
        $tenant->loadCount(['users', 'products', 'posts', 'orders']);
        
        return view('superadmin.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        return view('superadmin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:tenants,code,' . $tenant->id,
            'domain' => 'required|string|max:255|unique:tenants,domain,' . $tenant->id,
            'database_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $tenant->update($request->all());

        return redirect()->route('superadmin.tenants.index')
                        ->with('success', 'Tenant đã được cập nhật!');
    }

    public function destroy(Tenant $tenant)
    {
        if ($tenant->code === 'default') {
            return back()->with('error', 'Không thể xóa tenant mặc định!');
        }

        $tenant->delete();

        return redirect()->route('superadmin.tenants.index')
                        ->with('success', 'Tenant đã được xóa!');
    }
}
