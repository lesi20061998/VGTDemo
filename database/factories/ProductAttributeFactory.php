<?php

namespace Database\Factories;

use App\Models\ProductAttribute;
use App\Models\AttributeGroup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductAttribute>
 */
class ProductAttributeFactory extends Factory
{
    protected $model = ProductAttribute::class;

    public function definition(): array
    {
        $name = $this->faker->word();

        return [
            'attribute_group_id' => AttributeGroup::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'type' => $this->faker->randomElement(['text', 'number', 'select', 'multiselect', 'color']),
            'is_filterable' => $this->faker->boolean(),
            'is_required' => $this->faker->boolean(),
            'sort_order' => $this->faker->randomNumber(2),
        ];
    }
}
