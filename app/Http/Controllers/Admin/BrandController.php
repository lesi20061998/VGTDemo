<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use App\Traits\HasAlerts;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    use HasAlerts;

    public function index(Request $request)
    {
        $brands = Brand::when($request->search, fn ($q) => $q->search($request->search))
            ->orderBy('name')
            ->paginate(config('app.admin_per_page', 20));

        return view('cms.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('cms.brands.create');
    }

    public function store(BrandRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $brand = Brand::create($data);

        if ($request->hasFile('logo')) {
            $brand->addMediaFromRequest('logo')->toMediaCollection('logos');
        }

        $projectCode = request()->route('projectCode');

        return redirect()->route('project.admin.brands.index', $projectCode)->with('alert', [
            'type' => 'success',
            'message' => 'Thêm thương hiệu thành công!',
        ]);
    }

    public function edit($projectCode, Brand $brand)
    {
        return view('cms.brands.edit', compact('brand'));
    }

    public function update(BrandRequest $request, $projectCode, Brand $brand)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $brand->update($data);

        if ($request->hasFile('logo')) {
            $brand->clearMediaCollection('logos');
            $brand->addMediaFromRequest('logo')->toMediaCollection('logos');
        }

        return redirect()->route('project.admin.brands.index', $projectCode)->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật thương hiệu thành công!',
        ]);
    }

    public function destroy($projectCode, Brand $brand)
    {
        $brand->delete();

        return redirect()->route('project.admin.brands.index', $projectCode)->with('alert', [
            'type' => 'success',
            'message' => 'Xóa thương hiệu thành công!',
        ]);
    }
}
