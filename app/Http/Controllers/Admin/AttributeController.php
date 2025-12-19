<?php

// MODIFIED: 2025-01-21

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttributeRequest;
use App\Http\Requests\AttributeValueRequest;
use App\Models\AttributeGroup;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    // ===== ATTRIBUTE GROUPS =====
    public function indexGroups($projectCode, Request $request)
    {
        $groups = AttributeGroup::when($request->search, fn ($q) => $q->search($request->search))
            ->orderBy('sort_order')
            ->paginate(config('app.admin_per_page', 20));

        return view('cms.attributes.groups.index', compact('groups'));
    }

    public function createGroup($projectCode)
    {
        return view('cms.attributes.groups.create');
    }

    public function storeGroup(AttributeRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        AttributeGroup::create($data);

        $projectCode = request()->route('projectCode');

        return redirect()->route('project.admin.attributes.index', $projectCode)
            ->with('success', 'Nhóm thuộc tính đã được tạo thành công.');
    }

    public function editGroup($projectCode, AttributeGroup $group)
    {
        return view('cms.attributes.groups.edit', compact('group'));
    }

    public function updateGroup(AttributeRequest $request, $projectCode, AttributeGroup $group)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $group->update($data);

        return redirect()->route('project.admin.attributes.index', $projectCode)
            ->with('success', 'Nhóm thuộc tính đã được cập nhật thành công.');
    }

    public function destroyGroup($projectCode, AttributeGroup $group)
    {
        $group->delete();

        return redirect()->route('project.admin.attributes.index', $projectCode)
            ->with('success', 'Nhóm thuộc tính đã được xóa thành công.');
    }

    // ===== PRODUCT ATTRIBUTES =====
    public function index($projectCode, Request $request)
    {
        $attributes = ProductAttribute::with(['group', 'values'])
            ->when($request->search, fn ($q) => $q->search($request->search))
            ->when($request->group_id, fn ($q) => $q->where('attribute_group_id', $request->group_id))
            ->orderBy('sort_order')
            ->paginate(config('app.admin_per_page', 20));

        $groups = AttributeGroup::active()->get();

        return view('cms.attributes.index', compact('attributes', 'groups'));
    }

    public function create($projectCode)
    {
        $groups = AttributeGroup::active()->get();

        return view('cms.attributes.create', compact('groups'));
    }

    public function store(AttributeRequest $request, $projectCode)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $attribute = ProductAttribute::create($data);

        // Create attribute values if provided
        if ($request->has('values')) {
            foreach ($request->values as $index => $valueData) {
                if (! empty($valueData['value'])) {
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

        return redirect()->route('project.admin.attributes.index', $projectCode)
            ->with('success', 'Thuộc tính đã được tạo thành công.');
    }

    public function show($projectCode, $id)
    {
        $attribute = ProductAttribute::with(['group', 'values'])->findOrFail($id);

        return view('cms.attributes.show', compact('attribute'));
    }

    public function edit($projectCode, $id)
    {
        $attribute = ProductAttribute::with('values')->findOrFail($id);
        $groups = AttributeGroup::active()->get();

        return view('cms.attributes.edit', compact('attribute', 'groups'));
    }

    public function update(AttributeRequest $request, $projectCode, $id)
    {
        $attribute = ProductAttribute::findOrFail($id);

        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $attribute->update($data);

        // Update attribute values
        if ($request->has('values')) {
            // Delete existing values
            $attribute->values()->delete();

            // Create new values
            foreach ($request->values as $index => $valueData) {
                if (! empty($valueData['value'])) {
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

        return redirect()->route('project.admin.attributes.index', $projectCode)
            ->with('success', 'Thuộc tính đã được cập nhật thành công.');
    }

    public function destroy($projectCode, $id)
    {
        $attribute = ProductAttribute::findOrFail($id);
        $attribute->delete();

        return redirect()->route('project.admin.attributes.index', $projectCode)
            ->with('success', 'Thuộc tính đã được xóa thành công.');
    }

    // ===== ATTRIBUTE VALUES =====
    public function indexValues($projectCode, $attributeId)
    {
        $attribute = ProductAttribute::findOrFail($attributeId);
        $values = $attribute->values()->orderBy('sort_order')->get();

        return view('cms.attributes.values.index', compact('attribute', 'values'));
    }

    public function createValue($projectCode, $attributeId)
    {
        $attribute = ProductAttribute::findOrFail($attributeId);

        return view('cms.attributes.values.create', compact('attribute'));
    }

    public function storeValue(AttributeValueRequest $request, $projectCode, $attributeId)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['value']);
        $data['product_attribute_id'] = $attributeId; // Ensure the attribute ID is set

        ProductAttributeValue::create($data);

        return redirect()->route('project.admin.attributes.show', [$projectCode, $attributeId])
            ->with('success', 'Giá trị thuộc tính đã được tạo thành công.');
    }

    public function editValue($projectCode, $attributeId, $valueId)
    {
        $attribute = ProductAttribute::findOrFail($attributeId);
        $value = ProductAttributeValue::findOrFail($valueId);

        return view('cms.attributes.values.edit', compact('attribute', 'value'));
    }

    public function updateValue(AttributeValueRequest $request, $projectCode, $attributeId, $valueId)
    {
        $attribute = ProductAttribute::findOrFail($attributeId);
        $value = ProductAttributeValue::findOrFail($valueId);

        $data = $request->validated();
        $data['slug'] = Str::slug($data['value']);

        $value->update($data);

        return redirect()->route('project.admin.attributes.show', [$projectCode, $attribute->id])
            ->with('success', 'Giá trị thuộc tính đã được cập nhật thành công.');
    }

    public function destroyValue($projectCode, $attributeId, $valueId)
    {
        $attribute = ProductAttribute::findOrFail($attributeId);
        $value = ProductAttributeValue::findOrFail($valueId);

        $value->delete();

        return redirect()->route('project.admin.attributes.show', [$projectCode, $attribute->id])
            ->with('success', 'Giá trị thuộc tính đã được xóa thành công.');
    }
}
