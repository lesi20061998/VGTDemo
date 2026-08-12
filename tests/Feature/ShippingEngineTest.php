<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ShippingCarrier;
use App\Models\ShippingRateVersion;
use App\Models\ShippingRule;
use App\Services\Shipping\ConditionEvaluator;
use App\Services\Shipping\ShippingEngine;
use App\Services\Shipping\ShippingEvaluationContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingEngineTest extends TestCase
{
    use RefreshDatabase;

    protected Project $project;
    protected ShippingEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->engine = new ShippingEngine(new ConditionEvaluator());
        $this->project = Project::factory()->create();

        // Create standard carrier and version
        $carrier = ShippingCarrier::create([
            'project_id' => $this->project->id,
            'name' => 'Local Standard',
            'code' => 'local',
            'type' => 'local',
            'status' => true,
        ]);

        $this->version = ShippingRateVersion::create([
            'shipping_carrier_id' => $carrier->id,
            'version_name' => 'Test Version',
            'is_active' => true,
        ]);
    }

    public function test_default_rate_is_applied()
    {
        // Default Rule
        ShippingRule::create([
            'shipping_rate_version_id' => $this->version->id,
            'name' => 'Default Rate',
            'priority' => 0,
            'action_type' => 'override',
            'fee' => 30000,
            'status' => true,
        ]);

        $context = new ShippingEvaluationContext(orderValue: 100000);
        $result = $this->engine->calculate($this->project, $context);

        $this->assertTrue($result['success']);
        $this->assertEquals(30000, $result['final_fee']);
    }

    public function test_free_shipping_overrides_base_rate()
    {
        // Base Rule
        ShippingRule::create([
            'shipping_rate_version_id' => $this->version->id,
            'name' => 'Base Rate',
            'priority' => 0,
            'action_type' => 'override',
            'fee' => 30000,
            'status' => true,
        ]);

        // Free Ship Rule
        $freeRule = ShippingRule::create([
            'shipping_rate_version_id' => $this->version->id,
            'name' => 'Free Ship > 2M',
            'priority' => 10,
            'action_type' => 'free',
            'fee' => 0,
            'status' => true,
        ]);

        $freeRule->conditions()->create([
            'condition_type' => 'order_value',
            'operator' => '>=',
            'value_1' => 2000000,
        ]);

        // Context: 1.5M (Should not be free)
        $context1 = new ShippingEvaluationContext(orderValue: 1500000);
        $result1 = $this->engine->calculate($this->project, $context1);
        $this->assertEquals(30000, $result1['final_fee']);

        // Context: 2.5M (Should be free)
        $context2 = new ShippingEvaluationContext(orderValue: 2500000);
        $result2 = $this->engine->calculate($this->project, $context2);
        $this->assertEquals(0, $result2['final_fee']);
    }

    public function test_surcharge_is_added_to_base_rate()
    {
        // Base Rule
        ShippingRule::create([
            'shipping_rate_version_id' => $this->version->id,
            'name' => 'Base Rate',
            'priority' => 0,
            'action_type' => 'override',
            'fee' => 30000,
            'status' => true,
        ]);

        // Oversize Surcharge
        $surcharge = ShippingRule::create([
            'shipping_rate_version_id' => $this->version->id,
            'name' => 'Oversize',
            'priority' => 5,
            'action_type' => 'add',
            'fee' => 50000,
            'is_surcharge' => true,
            'status' => true,
        ]);

        $surcharge->conditions()->create([
            'condition_type' => 'weight',
            'operator' => '>',
            'value_1' => 10,
        ]);

        // Weight = 5kg (No Surcharge)
        $context1 = new ShippingEvaluationContext(actualWeight: 5, volumetricWeight: 5);
        $result1 = $this->engine->calculate($this->project, $context1);
        $this->assertEquals(30000, $result1['final_fee']);

        // Weight = 15kg (Surcharge)
        $context2 = new ShippingEvaluationContext(actualWeight: 15, volumetricWeight: 15);
        $result2 = $this->engine->calculate($this->project, $context2);
        $this->assertEquals(80000, $result2['final_fee']); // 30k base + 50k surcharge
    }

    public function test_chargeable_weight_uses_max_of_actual_and_volumetric()
    {
        // Surcharge
        $surcharge = ShippingRule::create([
            'shipping_rate_version_id' => $this->version->id,
            'name' => 'Heavy Item',
            'priority' => 5,
            'action_type' => 'override',
            'fee' => 100000, // Very high fee
            'status' => true,
        ]);

        $surcharge->conditions()->create([
            'condition_type' => 'weight',
            'operator' => '>',
            'value_1' => 10,
        ]);

        // Actual = 2kg, Volumetric = 15kg (Should trigger because Max(2,15) > 10)
        $context = new ShippingEvaluationContext(actualWeight: 2, volumetricWeight: 15);
        $result = $this->engine->calculate($this->project, $context);
        $this->assertEquals(100000, $result['final_fee']);
    }
}
