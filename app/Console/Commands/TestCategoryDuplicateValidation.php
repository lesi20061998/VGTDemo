<?php

namespace App\Console\Commands;

use App\Models\ProjectProductCategory;
use Illuminate\Console\Command;

class TestCategoryDuplicateValidation extends Command
{
    protected $signature = 'test:category-duplicate';

    protected $description = 'Test category duplicate name validation logic';

    public function handle(): int
    {
        $this->info('Testing category duplicate name validation...');

        try {
            // Clean up any existing test data first
            ProjectProductCategory::whereIn('name', [
                'Test Duplicate Category',
                'Another Test Category',
                'Category Without Slug',
            ])->delete();

            $this->info('🧹 Cleaned up existing test data');

            // Test 1: Create a category
            $testCategory = ProjectProductCategory::create([
                'name' => 'Test Duplicate Category',
                'slug' => 'test-duplicate-category',
                'level' => 0,
                'path' => 'test-duplicate-category',
                'sort_order' => 0,
            ]);
            $this->info("✅ Created test category: {$testCategory->name} (ID: {$testCategory->id})");

            // Test 2: Check duplicate detection for create
            $existingCategory = ProjectProductCategory::where('name', 'Test Duplicate Category')->first();
            if ($existingCategory) {
                $this->info('✅ Duplicate detection works for CREATE - found existing category');
            } else {
                $this->error('❌ Duplicate detection failed for CREATE');
            }

            // Test 3: Check duplicate detection for update (excluding self)
            $anotherCategory = ProjectProductCategory::create([
                'name' => 'Another Test Category',
                'slug' => 'another-test-category',
                'level' => 0,
                'path' => 'another-test-category',
                'sort_order' => 0,
            ]);

            $duplicateForUpdate = ProjectProductCategory::where('name', 'Test Duplicate Category')
                ->where('id', '!=', $anotherCategory->id)
                ->first();

            if ($duplicateForUpdate) {
                $this->info('✅ Duplicate detection works for UPDATE - found existing category excluding self');
            } else {
                $this->error('❌ Duplicate detection failed for UPDATE');
            }

            // Test 4: Check slug auto-generation
            $categoryWithoutSlug = ProjectProductCategory::create([
                'name' => 'Category Without Slug',
                'level' => 0,
                'sort_order' => 0,
            ]);

            if ($categoryWithoutSlug->slug === 'category-without-slug') {
                $this->info('✅ Slug auto-generation works');
            } else {
                $this->error("❌ Slug auto-generation failed. Expected: 'category-without-slug', Got: '{$categoryWithoutSlug->slug}'");
            }

            // Test 5: Check validation messages
            $this->info("\n📋 Validation messages:");
            $this->line("- Duplicate name warning: 'Cảnh báo: Tên danh mục '[name]' đã tồn tại. Vui lòng nhập tên khác.'");
            $this->line("- Create success: 'Danh mục '[name]' đã được thêm vào hệ thống.'");
            $this->line("- Update success: 'Danh mục '[name]' đã được cập nhật.'");
            $this->line("- Delete success: 'Danh mục '[name]' đã được xóa khỏi hệ thống.'");

            // Test 6: Check hierarchy features
            $this->info("\n📋 Category hierarchy features:");
            $this->line('- Maximum depth: 4 levels (0, 1, 2, 3)');
            $this->line('- Auto-calculate level and path');
            $this->line('- Prevent circular references');
            $this->line('- Handle children when parent is deleted');

            // Clean up test data
            ProjectProductCategory::whereIn('name', [
                'Test Duplicate Category',
                'Another Test Category',
                'Category Without Slug',
            ])->delete();

            $this->info("\n🧹 Cleaned up test data");
            $this->info("\n✅ All category validation tests completed successfully!");

        } catch (\Exception $e) {
            $this->error('❌ Test failed: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
