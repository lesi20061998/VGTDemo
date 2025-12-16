<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductAttributeValueMapping;
use App\Models\AttributeGroup;
use App\Services\ProductAttributeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAttributeSystemTest extends TestCase
{
    use RefreshDatabase;

    protected ProductAttributeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ProductAttributeService::class);
    }

    // ===== ATTRIBUTE GROUP TESTS =====

    public function test_can_create_attribute_group(): void
    {
        $group = AttributeGroup::factory()->create([
            'name' => 'Màu sắc',
            'slug' => 'mau-sac',
        ]);

        $this->assertDatabaseHas('attribute_groups', [
            'name' => 'Màu sắc',
            'slug' => 'mau-sac',
        ]);
    }

    // ===== PRODUCT ATTRIBUTE TESTS =====

    public function test_can_create_product_attribute(): void
    {
        $group = AttributeGroup::factory()->create();
        $attribute = ProductAttribute::factory()->create([
            'attribute_group_id' => $group->id,
            'name' => 'Color',
            'slug' => 'color',
            'type' => 'select',
        ]);

        $this->assertDatabaseHas('product_attributes', [
            'name' => 'Color',
            'type' => 'select',
        ]);

        $this->assertEquals($group->id, $attribute->attribute_group_id);
    }

    public function test_attribute_belongs_to_group(): void
    {
        $group = AttributeGroup::factory()->create();
        $attribute = ProductAttribute::factory()->create(['attribute_group_id' => $group->id]);

        $this->assertEquals($group->id, $attribute->group->id);
    }

    // ===== PRODUCT ATTRIBUTE VALUE TESTS =====

    public function test_can_create_attribute_value(): void
    {
        $attribute = ProductAttribute::factory()->create();
        $value = ProductAttributeValue::factory()->create([
            'product_attribute_id' => $attribute->id,
            'value' => 'red',
            'display_value' => 'Đỏ',
            'color_code' => '#FF0000',
        ]);

        $this->assertDatabaseHas('product_attribute_values', [
            'value' => 'red',
            'display_value' => 'Đỏ',
        ]);

        $this->assertEquals($attribute->id, $value->attribute->id);
    }

    public function test_attribute_can_have_multiple_values(): void
    {
        $attribute = ProductAttribute::factory()->create();

        $red = ProductAttributeValue::factory()->create([
            'product_attribute_id' => $attribute->id,
            'value' => 'red',
            'display_value' => 'Đỏ',
        ]);

        $blue = ProductAttributeValue::factory()->create([
            'product_attribute_id' => $attribute->id,
            'value' => 'blue',
            'display_value' => 'Xanh dương',
        ]);

        $this->assertEquals(2, $attribute->values()->count());
    }

    // ===== PRODUCT ATTRIBUTE MAPPING TESTS =====

    public function test_can_assign_single_attribute_value_to_product(): void
    {
        $product = Product::factory()->create();
        $attribute = ProductAttribute::factory()->create();
        $value = ProductAttributeValue::factory()->create(['product_attribute_id' => $attribute->id]);

        $this->service->addAttributeValue($product, $attribute, $value);

        $this->assertDatabaseHas('product_attribute_value_mappings', [
            'product_id' => $product->id,
            'product_attribute_id' => $attribute->id,
            'product_attribute_value_id' => $value->id,
        ]);
    }

    public function test_can_assign_multiple_values_of_same_attribute(): void
    {
        $product = Product::factory()->create();
        $attribute = ProductAttribute::factory()->create();

        $red = ProductAttributeValue::factory()->create([
            'product_attribute_id' => $attribute->id,
            'value' => 'red',
        ]);

        $blue = ProductAttributeValue::factory()->create([
            'product_attribute_id' => $attribute->id,
            'value' => 'blue',
        ]);

        // Gán cả Red và Blue cho product
        $this->service->addAttributeValue($product, $attribute, $red);
        $this->service->addAttributeValue($product, $attribute, $blue);

        // Product phải có 2 mappings cho attribute này
        $mappings = $product->attributeMappings()
            ->where('product_attribute_id', $attribute->id)
            ->count();

        $this->assertEquals(2, $mappings);
    }

    public function test_can_assign_multiple_attributes_with_values(): void
    {
        $product = Product::factory()->create();

        // Color attribute
        $colorAttr = ProductAttribute::factory()->create(['slug' => 'color']);
        $red = ProductAttributeValue::factory()->create(['product_attribute_id' => $colorAttr->id]);
        $blue = ProductAttributeValue::factory()->create(['product_attribute_id' => $colorAttr->id]);

        // Size attribute
        $sizeAttr = ProductAttribute::factory()->create(['slug' => 'size']);
        $sizeM = ProductAttributeValue::factory()->create(['product_attribute_id' => $sizeAttr->id]);
        $sizeL = ProductAttributeValue::factory()->create(['product_attribute_id' => $sizeAttr->id]);

        // Gán attributes
        $this->service->assignAttributes($product, [
            $colorAttr->id => [$red->id, $blue->id],
            $sizeAttr->id => [$sizeM->id, $sizeL->id],
        ]);

        // Kiểm tra số attribute khác nhau (unique trên collection)
        $this->assertEquals(2, $product->attributes->unique('id')->count());
        $this->assertEquals(4, $product->attributeMappings()->count());
    }

    public function test_cannot_assign_duplicate_mapping(): void
    {
        $product = Product::factory()->create();
        $attribute = ProductAttribute::factory()->create();
        $value = ProductAttributeValue::factory()->create(['product_attribute_id' => $attribute->id]);

        $this->service->addAttributeValue($product, $attribute, $value);
        $this->service->addAttributeValue($product, $attribute, $value);

        // Vẫn chỉ có 1 mapping vì có unique constraint
        $this->assertEquals(1, $product->attributeMappings()->count());
    }

    public function test_can_get_attribute_values_formatted(): void
    {
        $product = Product::factory()->create();

        $colorAttr = ProductAttribute::factory()->create([
            'name' => 'Màu sắc',
            'slug' => 'color',
            'type' => 'select',
        ]);

        $red = ProductAttributeValue::factory()->create([
            'product_attribute_id' => $colorAttr->id,
            'value' => 'red',
            'display_value' => 'Đỏ',
        ]);

        $this->service->assignAttributes($product, [
            $colorAttr->id => [$red->id],
        ]);

        $formatted = $this->service->getProductAttributesFormatted($product);

        $this->assertArrayHasKey('color', $formatted);
        $this->assertEquals('Màu sắc', $formatted['color']['name']);
        $this->assertEquals('select', $formatted['color']['type']);
        $this->assertCount(1, $formatted['color']['values']);
    }

    public function test_can_check_attribute_value_exists(): void
    {
        $product = Product::factory()->create();
        $attribute = ProductAttribute::factory()->create();
        $value = ProductAttributeValue::factory()->create(['product_attribute_id' => $attribute->id]);

        $this->service->addAttributeValue($product, $attribute, $value);

        $this->assertTrue($this->service->hasAttributeValue($product, $attribute->slug, $value->id));
    }

    public function test_can_remove_attribute_value(): void
    {
        $product = Product::factory()->create();
        $attribute = ProductAttribute::factory()->create();
        $value = ProductAttributeValue::factory()->create(['product_attribute_id' => $attribute->id]);

        $this->service->addAttributeValue($product, $attribute, $value);
        $this->assertEquals(1, $product->attributeMappings()->count());

        $this->service->removeAttributeValue($product, $attribute, $value);
        $this->assertEquals(0, $product->attributeMappings()->count());
    }

    public function test_can_clear_all_attributes(): void
    {
        $product = Product::factory()->create();

        $attr1 = ProductAttribute::factory()->create();
        $attr2 = ProductAttribute::factory()->create();

        $val1 = ProductAttributeValue::factory()->create(['product_attribute_id' => $attr1->id]);
        $val2 = ProductAttributeValue::factory()->create(['product_attribute_id' => $attr2->id]);

        $this->service->assignAttributes($product, [
            $attr1->id => [$val1->id],
            $attr2->id => [$val2->id],
        ]);

        $this->assertEquals(2, $product->attributeMappings()->count());

        $this->service->clearAttributes($product);
        $this->assertEquals(0, $product->attributeMappings()->count());
    }

    public function test_product_relationship_attributes(): void
    {
        $product = Product::factory()->create();

        $colorAttr = ProductAttribute::factory()->create(['slug' => 'color']);
        $red = ProductAttributeValue::factory()->create(['product_attribute_id' => $colorAttr->id]);

        $sizeAttr = ProductAttribute::factory()->create(['slug' => 'size']);
        $sizeM = ProductAttributeValue::factory()->create(['product_attribute_id' => $sizeAttr->id]);

        $this->service->assignAttributes($product, [
            $colorAttr->id => [$red->id],
            $sizeAttr->id => [$sizeM->id],
        ]);

        // Test distinct attributes
        $attributes = $product->attributes()->distinct()->get();
        $this->assertEquals(2, $attributes->count());

        // Test attribute values
        $values = $product->attributeValues()->get();
        $this->assertEquals(2, $values->count());
    }
}
