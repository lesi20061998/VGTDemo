<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\FeaturePack;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FeaturePackController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (! auth()->user() || ! auth()->user()->isSuperAdmin()) {
                    abort(403, 'Chỉ có Super Administrator mới có quyền truy cập quản lý Gói tính năng.');
                }

                return $next($request);
            }),
        ];
    }

    public function index()
    {
        $featurePacks = FeaturePack::orderBy('group_name')->orderBy('name')->get();

        return view('superadmin.feature-packs.index', compact('featurePacks'));
    }

    public function create()
    {
        return view('superadmin.feature-packs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:feature_packs',
            'group_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        FeaturePack::create($validated);

        return redirect()->route('superadmin.feature-packs.index')->with('alert', [
            'type' => 'success',
            'message' => 'Tạo Feature Pack thành công!',
        ]);
    }

    public function edit(FeaturePack $featurePack)
    {
        return view('superadmin.feature-packs.edit', compact('featurePack'));
    }

    public function update(Request $request, FeaturePack $featurePack)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:feature_packs,code,'.$featurePack->id,
            'group_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Default checkbox value fix
        if (! $request->has('is_active')) {
            $validated['is_active'] = false;
        }

        $featurePack->update($validated);

        return redirect()->route('superadmin.feature-packs.index')->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật Feature Pack thành công!',
        ]);
    }

    public function destroy(FeaturePack $featurePack)
    {
        $featurePack->delete();

        return redirect()->route('superadmin.feature-packs.index')->with('alert', [
            'type' => 'success',
            'message' => 'Xóa Feature Pack thành công!',
        ]);
    }
}
