<?php
// MODIFIED: 2025-01-21

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\AttributeGroup;
use App\Models\ProductAttributeValue;
use App\Http\Requests\AttributeRequest;
use App\Http\Requests\AttributeValueRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    // ===== ATTRIBUTE GROUPS =====
    public function indexGroups(Request $request)
    {
        $groups = AttributeGroup::when($request->search, fn($q) => $q->search($request->search))
            ->orderBy('sort_order')
            ->paginate(config('app.admin_per_page', 20));

        return view('cms.attributes.groups.index', compact('groups'));
    }

    public function createGroup()
    {
        return view('cms.attributes.groups.create');
    }

    public function storeGroup(AttributeRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        AttributeGroup::create($data);

        return redirect()->route('cms.attributes.groups.index')
                        ->with('success', 'Nhóm thuộc tính đã được tạo thành công.');
    }

    public function editGroup(AttributeGroup $group)
    {
        return view('cms.attributes.groups.edit', compact('group'));
    }

    public function updateGroup(AttributeRequest $request, AttributeGroup $group)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $group->update($data);

        return redirect()->route('cms.attributes.groups.index')
                        ->with('success', 'Nhóm thuộc tính đã được cập nhật thành công.');
    }

    public function destroyGroup(AttributeGroup $group)
    {
        $group->delete();

        return redirect()->route('cms.attributes.groups.index')
                        ->with('success', 'Nhóm thuộc tính đã được xóa thành công.');
    }

    // ===== PRODUCT ATTRIBUTES =====
    public function index(Request $request)
    {
        $attributes = ProductAttribute::with(['group', 'values'])
            ->when($request->search, fn($q) => $q->search($request->search))
            ->when($request->group_id, fn($q) => $q->where('attribute_group_id', $request->group_id))
            ->orderBy('sort_order')
            ->paginate(config('app.admin_per_page', 20));

        $groups = AttributeGroup::active()->get();

        return view('cms.attributes.index', compact('attributes', 'groups'));
    }

    public function create()
    {
        $groups = AttributeGroup::active()->get();
        return view('cms.attributes.create', compact('groups'));
    }

    public function store(AttributeRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        
        $attribute = ProductAttribute::create($data);

        // Create attribute values if provided
        if ($request->has('values')) {
            foreach ($request->values as $index => $valueData) {
                if (!empty($valueData['value'])) {
                    ProductAttributeValue::create([
                        'product_attribute_id' => $attribute->id,
                        'value' => $valueData['value'],
                        'display_value' => $valueData['display_value'] ?? null,
                        'color_code' => $valueData['color_code'] ?? null,
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('cms.attributes.index')
                        ->with('success', 'Thuộc tính đã được tạo thành công.');
    }

    public function show(ProductAttribute $attribute)
    {
        $attribute->load(['group', 'values']);
        return view('cms.attributes.show', compact('attribute'));
    }

    public function edit(ProductAttribute $attribute)
    {
        $groups = AttributeGroup::active()->get();
        $attribute->load('values');
        
        return view('cms.attributes.edit', compact('attribute', 'groups'));
    }

    public function update(AttributeRequest $request, ProductAttribute $attribute)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        
        $attribute->update($data);

        // Update attribute values
        if ($request->has('values')) {
            // Delete existing values
            $attribute->values()->delete();
            
            // Create new values
            foreach ($request->values as $index => $valueData) {
                if (!empty($valueData['value'])) {
                    ProductAttributeValue::create([
                        'product_attribute_id' => $attribute->id,
                        'value' => $valueData['value'],
                        'display_value' => $valueData['display_value'] ?? null,
                        'color_code' => $valueData['color_code'] ?? null,
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('cms.attributes.index')
                        ->with('success', 'Thuộc tính đã được cập nhật thành công.');
    }

    public function destroy(ProductAttribute $attribute)
    {
        $attribute->delete();
        
        return redirect()->route('cms.attributes.index')
                        ->with('success', 'Thuộc tính đã được xóa thành công.');
    }

    // ===== ATTRIBUTE VALUES =====
    public function indexValues(ProductAttribute $attribute)
    {
        $values = $attribute->values()->orderBy('sort_order')->get();
        return view('cms.attributes.values.index', compact('attribute', 'values'));
    }

    public function createValue(ProductAttribute $attribute)
    {
        return view('cms.attributes.values.create', compact('attribute'));
    }

    public function storeValue(AttributeValueRequest $request, ProductAttribute $attribute)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['value']);

        $attribute->values()->create($data);

        return redirect()->route('cms.attributes.values.index', $attribute)
                        ->with('success', 'Giá trị thuộc tính đã được tạo thành công.');
    }

    public function editValue(ProductAttribute $attribute, ProductAttributeValue $value)
    {
        return view('cms.attributes.values.edit', compact('attribute', 'value'));
    }

    public function updateValue(AttributeValueRequest $request, ProductAttribute $attribute, ProductAttributeValue $value)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['value']);

        $value->update($data);

        return redirect()->route('cms.attributes.values.index', $attribute)
                        ->with('success', 'Giá trị thuộc tính đã được cập nhật thành công.');
    }

    public function destroyValue(ProductAttribute $attribute, ProductAttributeValue $value)
    {
        $value->delete();

        return redirect()->route('cms.attributes.values.index', $attribute)
                        ->with('success', 'Giá trị thuộc tính đã được xóa thành công.');
    }
}
