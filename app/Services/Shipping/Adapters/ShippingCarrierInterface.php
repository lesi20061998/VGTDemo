<?php

namespace App\Services\Shipping\Adapters;

use App\Models\Project;
use App\Services\Shipping\ShippingEvaluationContext;

interface ShippingCarrierInterface
{
    /**
     * Calculate shipping rate for the given context.
     *
     * @param Project $project
     * @param ShippingEvaluationContext $context
     * @return array
     */
    public function calculateRate(Project $project, ShippingEvaluationContext $context): array;

    /**
     * Create a shipment with the carrier via API.
     *
     * @param array $shipmentData
     * @return array
     */
    public function createShipment(array $shipmentData): array;

    /**
     * Track a shipment.
     *
     * @param string $trackingNumber
     * @return array
     */
    public function trackShipment(string $trackingNumber): array;
}
