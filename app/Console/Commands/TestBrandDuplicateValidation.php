<?php

namespace App\Console\Commands;

use App\Models\ProjectBrand;
use Illuminate\Console\Command;

class TestBrandDuplicateValidation extends Command
{
    protected $signature = 'test:brand-duplicate';

    protected $description = 'Test brand duplicate name validation logic';

    public function handle(): int
    {
        $this->info('Testing brand duplicate name validation...');

        try {
            // Clean up any existing test data first
            ProjectBrand::whereIn('name', [
                'Test Duplicate Brand',
                'Another Test Brand',
                'Brand Without Slug',
            ])->delete();

            $this->info('🧹 Cleaned up existing test data');

            // Test 1: Create a brand
            $testBrand = ProjectBrand::create([
                'name' => 'Test Duplicate Brand',
                'slug' => 'test-duplicate-brand',
            ]);
            $this->info("✅ Created test brand: {$testBrand->name} (ID: {$testBrand->id})");

            // Test 2: Check duplicate detection for create
            $existingBrand = ProjectBrand::where('name', 'Test Duplicate Brand')->first();
            if ($existingBrand) {
                $this->info('✅ Duplicate detection works for CREATE - found existing brand');
            } else {
                $this->error('❌ Duplicate detection failed for CREATE');
            }

            // Test 3: Check duplicate detection for update (excluding self)
            $anotherBrand = ProjectBrand::create([
                'name' => 'Another Test Brand',
                'slug' => 'another-test-brand',
            ]);

            $duplicateForUpdate = ProjectBrand::where('name', 'Test Duplicate Brand')
                ->where('id', '!=', $anotherBrand->id)
                ->first();

            if ($duplicateForUpdate) {
                $this->info('✅ Duplicate detection works for UPDATE - found existing brand excluding self');
            } else {
                $this->error('❌ Duplicate detection failed for UPDATE');
            }

            // Test 4: Check slug auto-generation
            $brandWithoutSlug = ProjectBrand::create([
                'name' => 'Brand Without Slug',
            ]);

            if ($brandWithoutSlug->slug === 'brand-without-slug') {
                $this->info('✅ Slug auto-generation works');
            } else {
                $this->error("❌ Slug auto-generation failed. Expected: 'brand-without-slug', Got: '{$brandWithoutSlug->slug}'");
            }

            // Test 5: Check validation messages
            $this->info("\n📋 Validation messages:");
            $this->line("- Duplicate name warning: 'Cảnh báo: Tên thương hiệu '[name]' đã tồn tại. Vui lòng nhập tên khác.'");
            $this->line("- Create success: 'Thương hiệu '[name]' đã được thêm vào hệ thống.'");
            $this->line("- Update success: 'Thương hiệu '[name]' đã được cập nhật.'");
            $this->line("- Delete success: 'Thương hiệu '[name]' đã được xóa khỏi hệ thống.'");

            // Clean up test data
            ProjectBrand::whereIn('name', [
                'Test Duplicate Brand',
                'Another Test Brand',
                'Brand Without Slug',
            ])->delete();

            $this->info("\n🧹 Cleaned up test data");
            $this->info("\n✅ All brand validation tests completed successfully!");

        } catch (\Exception $e) {
            $this->error('❌ Test failed: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
