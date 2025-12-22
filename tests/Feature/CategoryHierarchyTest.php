<?php

namespace Tests\Feature;

use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryHierarchyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for authentication
        $this->user = User::factory()->create();
    }

    public function test_category_children_levels_update_when_parent_changes(): void
    {
        // Create category hierarchy:
        // Level 0: Fashion (parent)
        // Level 1: Accessories (child of Fashion)
        // Level 2: Bags (child of Accessories)
        // Level 2: Jewelry (child of Accessories)

        $fashion = ProductCategory::create([
            'name' => 'Phụ kiện thời trang',
            'slug' => 'phu-kien-thoi-trang',
            'level' => 0,
            'path' => 'phu-kien-thoi-trang',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $accessories = ProductCategory::create([
            'name' => 'Phụ kiện',
            'slug' => 'phu-kien',
            'level' => 1,
            'path' => 'phu-kien-thoi-trang/phu-kien',
            'parent_id' => $fashion->id,
            'is_active' => true,
        ]);

        $bags = ProductCategory::create([
            'name' => 'Túi xách',
            'slug' => 'tui-xach',
            'level' => 2,
            'path' => 'phu-kien-thoi-trang/phu-kien/tui-xach',
            'parent_id' => $accessories->id,
            'is_active' => true,
        ]);

        $jewelry = ProductCategory::create([
            'name' => 'Trang sức',
            'slug' => 'trang-suc',
            'level' => 2,
            'path' => 'phu-kien-thoi-trang/phu-kien/trang-suc',
            'parent_id' => $accessories->id,
            'is_active' => true,
        ]);

        // Now manually update "Accessories" to be a root category (level 0)
        $accessories->update([
            'parent_id' => null,
            'level' => 0,
            'path' => 'phu-kien',
        ]);

        // Manually update children levels and paths (simulating the controller logic)
        $accessories->updateDescendantsHierarchy();

        // Refresh models from database
        $accessories->refresh();
        $bags->refresh();
        $jewelry->refresh();

        // Assert that Accessories is now level 0
        $this->assertEquals(0, $accessories->level);
        $this->assertEquals('phu-kien', $accessories->path);

        // Assert that children (Bags and Jewelry) are now level 1
        $this->assertEquals(1, $bags->level);
        $this->assertEquals('phu-kien/tui-xach', $bags->path);

        $this->assertEquals(1, $jewelry->level);
        $this->assertEquals('phu-kien/trang-suc', $jewelry->path);
    }

    public function test_category_children_levels_update_when_moved_to_different_parent(): void
    {
        // Create categories
        $electronics = ProductCategory::create([
            'name' => 'Điện tử',
            'slug' => 'dien-tu',
            'level' => 0,
            'path' => 'dien-tu',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $fashion = ProductCategory::create([
            'name' => 'Thời trang',
            'slug' => 'thoi-trang',
            'level' => 0,
            'path' => 'thoi-trang',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $accessories = ProductCategory::create([
            'name' => 'Phụ kiện',
            'slug' => 'phu-kien',
            'level' => 1,
            'path' => 'dien-tu/phu-kien',
            'parent_id' => $electronics->id,
            'is_active' => true,
        ]);

        $bags = ProductCategory::create([
            'name' => 'Túi xách',
            'slug' => 'tui-xach',
            'level' => 2,
            'path' => 'dien-tu/phu-kien/tui-xach',
            'parent_id' => $accessories->id,
            'is_active' => true,
        ]);

        // Move "Accessories" from "Electronics" to "Fashion"
        $accessories->update([
            'parent_id' => $fashion->id,
            'level' => 1,
            'path' => 'thoi-trang/phu-kien',
        ]);

        // Update children
        $accessories->updateDescendantsHierarchy();

        // Refresh models
        $accessories->refresh();
        $bags->refresh();

        // Assert that Accessories is now under Fashion (level 1)
        $this->assertEquals(1, $accessories->level);
        $this->assertEquals('thoi-trang/phu-kien', $accessories->path);

        // Assert that Bags is now level 2 under the new hierarchy
        $this->assertEquals(2, $bags->level);
        $this->assertEquals('thoi-trang/phu-kien/tui-xach', $bags->path);
    }

    public function test_deep_hierarchy_updates_correctly(): void
    {
        // Create a deep hierarchy (4 levels)
        $level0 = ProductCategory::create([
            'name' => 'Level 0',
            'slug' => 'level-0',
            'level' => 0,
            'path' => 'level-0',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $level1 = ProductCategory::create([
            'name' => 'Level 1',
            'slug' => 'level-1',
            'level' => 1,
            'path' => 'level-0/level-1',
            'parent_id' => $level0->id,
            'is_active' => true,
        ]);

        $level2 = ProductCategory::create([
            'name' => 'Level 2',
            'slug' => 'level-2',
            'level' => 2,
            'path' => 'level-0/level-1/level-2',
            'parent_id' => $level1->id,
            'is_active' => true,
        ]);

        $level3 = ProductCategory::create([
            'name' => 'Level 3',
            'slug' => 'level-3',
            'level' => 3,
            'path' => 'level-0/level-1/level-2/level-3',
            'parent_id' => $level2->id,
            'is_active' => true,
        ]);

        // Move level1 to root (level 0)
        $level1->update([
            'parent_id' => null,
            'level' => 0,
            'path' => 'level-1',
        ]);

        // Update descendants
        $level1->updateDescendantsHierarchy();

        // Refresh all models
        $level1->refresh();
        $level2->refresh();
        $level3->refresh();

        // Assert all levels have been adjusted
        $this->assertEquals(0, $level1->level);
        $this->assertEquals('level-1', $level1->path);

        $this->assertEquals(1, $level2->level);
        $this->assertEquals('level-1/level-2', $level2->path);

        $this->assertEquals(2, $level3->level);
        $this->assertEquals('level-1/level-2/level-3', $level3->path);
    }

    public function test_category_children_levels_update_when_moved_down_hierarchy(): void
    {
        // Create a hierarchy where we'll move a category down
        // Level 0: Electronics
        // Level 1: Accessories (will be moved to level 2 under Fashion)
        // Level 2: Bags (should become level 3)
        // Level 2: Jewelry (should become level 3)

        $electronics = ProductCategory::create([
            'name' => 'Điện tử',
            'slug' => 'dien-tu',
            'level' => 0,
            'path' => 'dien-tu',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $fashion = ProductCategory::create([
            'name' => 'Thời trang',
            'slug' => 'thoi-trang',
            'level' => 0,
            'path' => 'thoi-trang',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $fashionAccessories = ProductCategory::create([
            'name' => 'Phụ kiện thời trang',
            'slug' => 'phu-kien-thoi-trang',
            'level' => 1,
            'path' => 'thoi-trang/phu-kien-thoi-trang',
            'parent_id' => $fashion->id,
            'is_active' => true,
        ]);

        $accessories = ProductCategory::create([
            'name' => 'Phụ kiện',
            'slug' => 'phu-kien',
            'level' => 1,
            'path' => 'dien-tu/phu-kien',
            'parent_id' => $electronics->id,
            'is_active' => true,
        ]);

        $bags = ProductCategory::create([
            'name' => 'Túi xách',
            'slug' => 'tui-xach',
            'level' => 2,
            'path' => 'dien-tu/phu-kien/tui-xach',
            'parent_id' => $accessories->id,
            'is_active' => true,
        ]);

        $jewelry = ProductCategory::create([
            'name' => 'Trang sức',
            'slug' => 'trang-suc',
            'level' => 2,
            'path' => 'dien-tu/phu-kien/trang-suc',
            'parent_id' => $accessories->id,
            'is_active' => true,
        ]);

        // Move "Accessories" from Electronics (level 1) to under Fashion Accessories (level 2)
        $accessories->update([
            'parent_id' => $fashionAccessories->id,
            'level' => 2, // Moving down from level 1 to level 2
            'path' => 'thoi-trang/phu-kien-thoi-trang/phu-kien',
        ]);

        // Update children
        $accessories->updateDescendantsHierarchy();

        // Refresh models
        $accessories->refresh();
        $bags->refresh();
        $jewelry->refresh();

        // Assert that Accessories is now under Fashion Accessories (level 2)
        $this->assertEquals(2, $accessories->level);
        $this->assertEquals('thoi-trang/phu-kien-thoi-trang/phu-kien', $accessories->path);

        // Assert that children (Bags and Jewelry) are now level 3
        $this->assertEquals(3, $bags->level);
        $this->assertEquals('thoi-trang/phu-kien-thoi-trang/phu-kien/tui-xach', $bags->path);

        $this->assertEquals(3, $jewelry->level);
        $this->assertEquals('thoi-trang/phu-kien-thoi-trang/phu-kien/trang-suc', $jewelry->path);
    }

    public function test_move_to_parent_method_works_correctly(): void
    {
        // Create initial hierarchy
        $electronics = ProductCategory::create([
            'name' => 'Điện tử',
            'slug' => 'dien-tu',
            'level' => 0,
            'path' => 'dien-tu',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $fashion = ProductCategory::create([
            'name' => 'Thời trang',
            'slug' => 'thoi-trang',
            'level' => 0,
            'path' => 'thoi-trang',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $accessories = ProductCategory::create([
            'name' => 'Phụ kiện',
            'slug' => 'phu-kien',
            'level' => 1,
            'path' => 'dien-tu/phu-kien',
            'parent_id' => $electronics->id,
            'is_active' => true,
        ]);

        $bags = ProductCategory::create([
            'name' => 'Túi xách',
            'slug' => 'tui-xach',
            'level' => 2,
            'path' => 'dien-tu/phu-kien/tui-xach',
            'parent_id' => $accessories->id,
            'is_active' => true,
        ]);

        // Use moveToParent method to move accessories to fashion
        $accessories->moveToParent($fashion->id);

        // Refresh models
        $accessories->refresh();
        $bags->refresh();

        // Assert correct hierarchy
        $this->assertEquals($fashion->id, $accessories->parent_id);
        $this->assertEquals(1, $accessories->level);
        $this->assertEquals('thoi-trang/phu-kien', $accessories->path);

        $this->assertEquals(2, $bags->level);
        $this->assertEquals('thoi-trang/phu-kien/tui-xach', $bags->path);

        // Test moving to root (no parent)
        $accessories->moveToParent(null);

        // Refresh models
        $accessories->refresh();
        $bags->refresh();

        // Assert correct hierarchy
        $this->assertNull($accessories->parent_id);
        $this->assertEquals(0, $accessories->level);
        $this->assertEquals('phu-kien', $accessories->path);

        $this->assertEquals(1, $bags->level);
        $this->assertEquals('phu-kien/tui-xach', $bags->path);
    }

    public function test_hierarchy_validation_detects_inconsistencies(): void
    {
        // Create a category with inconsistent hierarchy (manually set wrong level)
        $parent = ProductCategory::create([
            'name' => 'Parent',
            'slug' => 'parent',
            'level' => 0,
            'path' => 'parent',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $child = ProductCategory::create([
            'name' => 'Child',
            'slug' => 'child',
            'level' => 3, // Wrong! Should be 1 (parent level + 1)
            'path' => 'parent/child',
            'parent_id' => $parent->id,
            'is_active' => true,
        ]);

        // Validation should detect the inconsistency
        $this->assertFalse($child->validateHierarchyConsistency());
        $this->assertTrue($parent->validateHierarchyConsistency());

        // Fix the inconsistency
        $child->fixHierarchyConsistency();
        $child->refresh();

        // Now it should be valid
        $this->assertTrue($child->validateHierarchyConsistency());
        $this->assertEquals(1, $child->level);
    }

    public function test_root_category_validation(): void
    {
        // Create a root category with wrong level
        $root = ProductCategory::create([
            'name' => 'Root',
            'slug' => 'root',
            'level' => 2, // Wrong! Root should be level 0
            'path' => 'root',
            'parent_id' => null,
            'is_active' => true,
        ]);

        // Should be invalid
        $this->assertFalse($root->validateHierarchyConsistency());

        // Fix it
        $root->fixHierarchyConsistency();
        $root->refresh();

        // Now should be valid
        $this->assertTrue($root->validateHierarchyConsistency());
        $this->assertEquals(0, $root->level);
    }

    public function test_maximum_depth_limit_is_enforced(): void
    {
        // Create a 4-level hierarchy (max allowed: 0,1,2,3)
        $level0 = ProductCategory::create([
            'name' => 'Level 0',
            'slug' => 'level-0',
            'level' => 0,
            'path' => 'level-0',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $level1 = ProductCategory::create([
            'name' => 'Level 1',
            'slug' => 'level-1',
            'level' => 1,
            'path' => 'level-0/level-1',
            'parent_id' => $level0->id,
            'is_active' => true,
        ]);

        $level2 = ProductCategory::create([
            'name' => 'Level 2',
            'slug' => 'level-2',
            'level' => 2,
            'path' => 'level-0/level-1/level-2',
            'parent_id' => $level1->id,
            'is_active' => true,
        ]);

        $level3 = ProductCategory::create([
            'name' => 'Level 3',
            'slug' => 'level-3',
            'level' => 3,
            'path' => 'level-0/level-1/level-2/level-3',
            'parent_id' => $level2->id,
            'is_active' => true,
        ]);

        // Level 3 should be valid (at max depth)
        $this->assertTrue($level3->validateHierarchyConsistency());
        $this->assertFalse($level3->canHaveChildren());

        // Trying to create level 4 should fail validation
        $level4 = ProductCategory::create([
            'name' => 'Level 4',
            'slug' => 'level-4',
            'level' => 4, // This exceeds MAX_DEPTH (3)
            'path' => 'level-0/level-1/level-2/level-3/level-4',
            'parent_id' => $level3->id,
            'is_active' => true,
        ]);

        $this->assertFalse($level4->validateHierarchyConsistency());

        // Test moveToParent with depth limit
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Moving category would exceed maximum depth');

        $newCategory = ProductCategory::create([
            'name' => 'New Category',
            'slug' => 'new-category',
            'level' => 0,
            'path' => 'new-category',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $newCategory->moveToParent($level3->id); // This should throw exception
    }

    public function test_can_have_children_method(): void
    {
        $level0 = ProductCategory::create([
            'name' => 'Level 0',
            'slug' => 'level-0',
            'level' => 0,
            'path' => 'level-0',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $level3 = ProductCategory::create([
            'name' => 'Level 3',
            'slug' => 'level-3',
            'level' => 3, // At max depth
            'path' => 'level-3',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $this->assertTrue($level0->canHaveChildren());
        $this->assertFalse($level3->canHaveChildren());
    }
}
