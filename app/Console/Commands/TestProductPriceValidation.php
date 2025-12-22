<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class TestProductPriceValidation extends Command
{
    protected $signature = 'test:product-price-validation';

    protected $description = 'Test product price validation rules';

    public function handle()
    {
        $this->info('Testing product price validation...');

        // Simple validation rules without database checks
        $rules = [
            'price' => 'nullable|numeric|min:0|max:9999999999999.99',
            'sale_price' => 'nullable|numeric|min:0|max:9999999999999.99|lt:price',
        ];

        // Test case 1: Valid prices
        $validData = [
            'price' => 1000000,
            'sale_price' => 800000,
        ];

        $validator = Validator::make($validData, $rules);

        if ($validator->passes()) {
            $this->info('✅ Valid prices passed validation');
        } else {
            $this->error('❌ Valid prices failed validation: '.implode(', ', $validator->errors()->all()));
        }

        // Test case 2: Price too large (15 digits)
        $invalidData = [
            'price' => 99999999999999.99, // 14 digits + 2 decimals - should pass
            'sale_price' => 800000,
        ];

        $validator = Validator::make($invalidData, $rules);

        if ($validator->fails()) {
            $this->error('❌ Valid 14-digit price incorrectly rejected: '.implode(', ', $validator->errors()->all()));
        } else {
            $this->info('✅ Valid 14-digit price correctly accepted');
        }

        // Test case 3: Price way too large (15+ digits)
        $tooLargeData = [
            'price' => 578567856785678, // 15 digits - should fail
            'sale_price' => 800000,
        ];

        $validator = Validator::make($tooLargeData, $rules);

        if ($validator->fails()) {
            $this->info('✅ 15-digit price correctly rejected: '.implode(', ', $validator->errors()->all()));
        } else {
            $this->error('❌ 15-digit price incorrectly accepted');
        }

        // Test case 4: Sale price larger than price
        $invalidSaleData = [
            'price' => 800000,
            'sale_price' => 1000000, // Larger than price
        ];

        $validator = Validator::make($invalidSaleData, $rules);

        if ($validator->fails()) {
            $this->info('✅ Sale price > price correctly rejected: '.implode(', ', $validator->errors()->all()));
        } else {
            $this->error('❌ Sale price > price incorrectly accepted');
        }

        $this->info("\nPrice validation test completed!");

        return 0;
    }
}
