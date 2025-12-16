<?php

namespace Database\Factories;

use App\Models\ProductAttributeValueMapping;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductAttributeValueMapping>
 */
class ProductAttributeValueMappingFactory extends Factory
{
    protected $model = ProductAttributeValueMapping::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'product_attribute_id' => ProductAttribute::factory(),
            'product_attribute_value_id' => ProductAttributeValue::factory(),
        ];
    }
}
