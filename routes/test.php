<?php

use App\Models\AttributeGroup;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProjectProduct;
use App\Models\ProjectProductAttributeValueMapping;
use Illuminate\Support\Facades\Route;

Route::get('/test-attributes', function () {
    // Create test data
    $group = AttributeGroup::firstOrCreate(['name' => 'Color', 'slug' => 'color']);

    $attribute = ProductAttribute::firstOrCreate([
        'name' => 'Color',
        'slug' => 'color',
        'attribute_group_id' => $group->id,
        'type' => 'select',
    ]);

    $redValue = ProductAttributeValue::firstOrCreate([
        'product_attribute_id' => $attribute->id,
        'value' => 'Red',
        'slug' => 'red',
    ]);

    $blueValue = ProductAttributeValue::firstOrCreate([
        'product_attribute_id' => $attribute->id,
        'value' => 'Blue',
        'slug' => 'blue',
    ]);

    // Create a test product
    $product = ProjectProduct::firstOrCreate([
        'name' => 'Test Product for Attributes',
        'sku' => 'TEST-ATTR-001',
    ], [
        'slug' => 'test-product-for-attributes',
        'product_type' => 'simple',
        'status' => 'draft',
        'language_id' => 1,
    ]);

    // Test adding attributes
    ProjectProductAttributeValueMapping::firstOrCreate([
        'product_id' => $product->id,
        'product_attribute_id' => $attribute->id,
        'product_attribute_value_id' => $redValue->id,
    ]);

    ProjectProductAttributeValueMapping::firstOrCreate([
        'product_id' => $product->id,
        'product_attribute_id' => $attribute->id,
        'product_attribute_value_id' => $blueValue->id,
    ]);

    // Update product to variable
    $product->update(['product_type' => 'variable']);

    // Test relationships
    $mappings = $product->attributeMappings()->with(['attribute', 'attributeValue'])->get();
    $attributes = $product->attributes()->get();
    $attributeValues = $product->attributeValues()->get();

    return response()->json([
        'product' => $product,
        'mappings_count' => $mappings->count(),
        'mappings' => $mappings,
        'attributes_count' => $attributes->count(),
        'attributes' => $attributes,
        'attribute_values_count' => $attributeValues->count(),
        'attribute_values' => $attributeValues,
        'success' => true,
    ]);
});
