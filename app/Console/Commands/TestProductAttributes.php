<?php

namespace App\Console\Commands;

use App\Models\AttributeGroup;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProjectProduct;
use App\Models\ProjectProductAttributeValueMapping;
use Illuminate\Console\Command;

class TestProductAttributes extends Command
{
    protected $signature = 'test:product-attributes';

    protected $description = 'Test product attributes functionality';

    public function handle(): int
    {
        $this->info('Testing Product Attributes...');

        try {
            // Create test data
            $group = AttributeGroup::firstOrCreate([
                'name' => 'Color',
                'slug' => 'color',
            ]);
            $this->info("✓ Created/found attribute group: {$group->name}");

            $attribute = ProductAttribute::firstOrCreate([
                'name' => 'Color',
                'slug' => 'color',
                'attribute_group_id' => $group->id,
                'type' => 'select',
            ]);
            $this->info("✓ Created/found attribute: {$attribute->name}");

            $redValue = ProductAttributeValue::firstOrCreate([
                'product_attribute_id' => $attribute->id,
                'value' => 'Red',
                'slug' => 'red',
            ]);
            $this->info("✓ Created/found value: {$redValue->value}");

            $blueValue = ProductAttributeValue::firstOrCreate([
                'product_attribute_id' => $attribute->id,
                'value' => 'Blue',
                'slug' => 'blue',
            ]);
            $this->info("✓ Created/found value: {$blueValue->value}");

            // Create test product
            $product = ProjectProduct::firstOrCreate([
                'name' => 'Test Product for Attributes',
                'sku' => 'TEST-ATTR-001',
            ], [
                'slug' => 'test-product-for-attributes',
                'product_type' => 'simple',
                'status' => 'draft',
                'language_id' => 1,
            ]);
            $this->info("✓ Created/found product: {$product->name}");

            // Test adding attributes
            $mapping1 = ProjectProductAttributeValueMapping::firstOrCreate([
                'product_id' => $product->id,
                'product_attribute_id' => $attribute->id,
                'product_attribute_value_id' => $redValue->id,
            ]);

            $mapping2 = ProjectProductAttributeValueMapping::firstOrCreate([
                'product_id' => $product->id,
                'product_attribute_id' => $attribute->id,
                'product_attribute_value_id' => $blueValue->id,
            ]);
            $this->info('✓ Created attribute mappings');

            // Update product to variable
            $product->update(['product_type' => 'variable']);
            $this->info('✓ Updated product type to variable');

            // Test relationships
            $mappings = $product->attributeMappings()->with(['attribute', 'attributeValue'])->get();
            $this->info("✓ Mappings count: {$mappings->count()}");

            $attributes = $product->attributes()->get();
            $this->info("✓ Attributes count: {$attributes->count()}");

            $attributeValues = $product->attributeValues()->get();
            $this->info("✓ Attribute values count: {$attributeValues->count()}");

            // Display details
            $this->table(['Mapping ID', 'Attribute', 'Value'], $mappings->map(function ($mapping) {
                return [
                    $mapping->id,
                    $mapping->attribute->name ?? 'N/A',
                    $mapping->attributeValue->value ?? 'N/A',
                ];
            }));

            $this->info('✅ All tests passed!');

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Test failed: {$e->getMessage()}");
            $this->error("Stack trace: {$e->getTraceAsString()}");

            return 1;
        }
    }
}
