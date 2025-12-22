<?php

namespace Tests\Feature;

use App\Models\AttributeGroup;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProjectProduct;
use App\Models\ProjectProductAttributeValueMapping;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAttributeUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_product_from_simple_to_variable_with_attributes(): void
    {
        // Create attribute group
        $group = AttributeGroup::factory()->create(['name' => 'Color']);

        // Create attribute
        $attribute = ProductAttribute::factory()->create([
            'name' => 'Color',
            'slug' => 'color',
            'attribute_group_id' => $group->id,
        ]);

        // Create attribute values
        $redValue = ProductAttributeValue::factory()->create([
            'product_attribute_id' => $attribute->id,
            'value' => 'Red',
            'slug' => 'red',
        ]);

        $blueValue = ProductAttributeValue::factory()->create([
            'product_attribute_id' => $attribute->id,
            'value' => 'Blue',
            'slug' => 'blue',
        ]);

        // Create a simple product
        $product = ProjectProduct::factory()->create([
            'name' => 'Test Product',
            'product_type' => 'simple',
        ]);

        // Update product to variable with attributes
        $response = $this->put(route('project.admin.products.update', ['test', $product->id]), [
            'name' => 'Test Product Updated',
            'sku' => $product->sku,
            'product_type' => 'variable',
            'attributes' => [
                $attribute->id => [$redValue->id, $blueValue->id],
            ],
        ]);

        $response->assertRedirect();

        // Check product was updated
        $product->refresh();
        $this->assertEquals('variable', $product->product_type);
        $this->assertEquals('Test Product Updated', $product->name);

        // Check attributes were assigned
        $this->assertEquals(2, $product->attributeMappings()->count());

        $mappings = $product->attributeMappings()->get();
        $this->assertTrue($mappings->contains('product_attribute_value_id', $redValue->id));
        $this->assertTrue($mappings->contains('product_attribute_value_id', $blueValue->id));
    }

    public function test_can_update_product_from_variable_to_simple_removes_attributes(): void
    {
        // Create attribute and values
        $group = AttributeGroup::factory()->create(['name' => 'Size']);
        $attribute = ProductAttribute::factory()->create([
            'name' => 'Size',
            'slug' => 'size',
            'attribute_group_id' => $group->id,
        ]);

        $smallValue = ProductAttributeValue::factory()->create([
            'product_attribute_id' => $attribute->id,
            'value' => 'Small',
            'slug' => 'small',
        ]);

        // Create a variable product with attributes
        $product = ProjectProduct::factory()->create([
            'name' => 'Variable Product',
            'product_type' => 'variable',
        ]);

        // Add attribute mapping
        ProjectProductAttributeValueMapping::create([
            'product_id' => $product->id,
            'product_attribute_id' => $attribute->id,
            'product_attribute_value_id' => $smallValue->id,
        ]);

        $this->assertEquals(1, $product->attributeMappings()->count());

        // Update product to simple
        $response = $this->put(route('project.admin.products.update', ['test', $product->id]), [
            'name' => 'Simple Product',
            'sku' => $product->sku,
            'product_type' => 'simple',
        ]);

        $response->assertRedirect();

        // Check product was updated
        $product->refresh();
        $this->assertEquals('simple', $product->product_type);
        $this->assertEquals('Simple Product', $product->name);

        // Check attributes were removed
        $this->assertEquals(0, $product->attributeMappings()->count());
    }
}
