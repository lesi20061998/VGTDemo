<?php

namespace Tests\Unit\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\RolePermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RolePermissionService $rolePermissionService;

    protected Tenant $tenant;

    protected Permission $permission1;

    protected Permission $permission2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rolePermissionService = new RolePermissionService;

        // Create a tenant first
        $this->tenant = Tenant::create([
            'name' => 'Test Tenant',
            'code' => 'test',
            'domain' => 'test.example.com',
            'database_name' => 'test_db',
            'status' => 'active',
        ]);

        // Create permissions
        $this->permission1 = Permission::create([
            'name' => 'manage_users',
            'display_name' => 'Manage Users',
            'description' => 'Create, edit, delete users',
            'group' => 'User Management',
        ]);

        $this->permission2 = Permission::create([
            'name' => 'view_reports',
            'display_name' => 'View Reports',
            'description' => 'View system reports',
            'group' => 'Reports',
        ]);
    }

    /**
     * Test role creation with permissions.
     */
    public function test_create_role_with_permissions(): void
    {
        $roleData = [
            'name' => 'test_role',
            'display_name' => 'Test Role',
            'description' => 'A test role',
            'level' => 3,
            'is_default' => false,
            'permissions' => [$this->permission1->id],
        ];

        $role = $this->rolePermissionService->createRole($roleData);

        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals('test_role', $role->name);
        $this->assertEquals('Test Role', $role->display_name);
        $this->assertEquals('A test role', $role->description);
        $this->assertEquals(3, $role->level);
        $this->assertFalse($role->is_default);

        // Assert permission was assigned
        $this->assertTrue($role->hasPermission('manage_users'));
    }

    /**
     * Test role update with permissions.
     */
    public function test_update_role_with_permissions(): void
    {
        $role = Role::factory()->create();

        $updateData = [
            'name' => 'updated_role',
            'display_name' => 'Updated Role',
            'description' => 'Updated description',
            'level' => 4,
            'is_default' => true,
            'permissions' => [$this->permission2->id],
        ];

        $updatedRole = $this->rolePermissionService->updateRole($role, $updateData);

        $this->assertEquals('updated_role', $updatedRole->name);
        $this->assertEquals('Updated Role', $updatedRole->display_name);
        $this->assertEquals('Updated description', $updatedRole->description);
        $this->assertEquals(4, $updatedRole->level);
        $this->assertTrue($updatedRole->is_default);

        // Assert permission was updated
        $this->assertTrue($updatedRole->hasPermission('view_reports'));
    }

    /**
     * Test role deletion with safety checks.
     */
    public function test_delete_role_with_safety_checks(): void
    {
        $role = Role::factory()->create(['is_default' => false]);

        $result = $this->rolePermissionService->deleteRole($role);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    /**
     * Test cannot delete default role.
     */
    public function test_cannot_delete_default_role(): void
    {
        $defaultRole = Role::factory()->create(['is_default' => true]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot delete default role.');

        $this->rolePermissionService->deleteRole($defaultRole);
    }

    /**
     * Test cannot delete role with assigned users.
     */
    public function test_cannot_delete_role_with_users(): void
    {
        $role = Role::factory()->create(['is_default' => false]);
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $user->roles()->attach($role);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot delete role that has assigned users.');

        $this->rolePermissionService->deleteRole($role);
    }

    /**
     * Test assign permission to role.
     */
    public function test_assign_permission_to_role(): void
    {
        $role = Role::factory()->create();

        $result = $this->rolePermissionService->assignPermissionToRole($role, $this->permission1->id);

        $this->assertTrue($result);
        $this->assertTrue($role->fresh()->hasPermission('manage_users'));
    }

    /**
     * Test assign permission prevents duplicates.
     */
    public function test_assign_permission_prevents_duplicates(): void
    {
        $role = Role::factory()->create();
        $role->permissions()->attach($this->permission1);

        $result = $this->rolePermissionService->assignPermissionToRole($role, $this->permission1->id);

        $this->assertFalse($result);
    }

    /**
     * Test revoke permission from role.
     */
    public function test_revoke_permission_from_role(): void
    {
        $role = Role::factory()->create();
        $role->permissions()->attach($this->permission1);

        $result = $this->rolePermissionService->revokePermissionFromRole($role, $this->permission1->id);

        $this->assertTrue($result);
        $this->assertFalse($role->fresh()->hasPermission('manage_users'));
    }

    /**
     * Test set role as default.
     */
    public function test_set_role_as_default(): void
    {
        $existingDefault = Role::factory()->create(['is_default' => true]);
        $role = Role::factory()->create(['is_default' => false]);

        $this->rolePermissionService->setRoleAsDefault($role);

        $this->assertTrue($role->fresh()->is_default);
        $this->assertFalse($existingDefault->fresh()->is_default);
    }

    /**
     * Test get permissions grouped.
     */
    public function test_get_permissions_grouped(): void
    {
        $groupedPermissions = $this->rolePermissionService->getPermissionsGrouped();

        $this->assertArrayHasKey('User Management', $groupedPermissions->toArray());
        $this->assertArrayHasKey('Reports', $groupedPermissions->toArray());
        $this->assertCount(1, $groupedPermissions['User Management']);
        $this->assertCount(1, $groupedPermissions['Reports']);
    }

    /**
     * Test create permission.
     */
    public function test_create_permission(): void
    {
        $permissionData = [
            'name' => 'test_permission',
            'display_name' => 'Test Permission',
            'description' => 'A test permission',
            'group' => 'Test',
        ];

        $permission = $this->rolePermissionService->createPermission($permissionData);

        $this->assertInstanceOf(Permission::class, $permission);
        $this->assertEquals('test_permission', $permission->name);
        $this->assertEquals('Test Permission', $permission->display_name);
        $this->assertEquals('A test permission', $permission->description);
        $this->assertEquals('Test', $permission->group);
    }

    /**
     * Test update permission.
     */
    public function test_update_permission(): void
    {
        $permission = Permission::factory()->create();

        $updateData = [
            'name' => 'updated_permission',
            'display_name' => 'Updated Permission',
            'description' => 'Updated description',
            'group' => 'Updated Group',
        ];

        $updatedPermission = $this->rolePermissionService->updatePermission($permission, $updateData);

        $this->assertEquals('updated_permission', $updatedPermission->name);
        $this->assertEquals('Updated Permission', $updatedPermission->display_name);
        $this->assertEquals('Updated description', $updatedPermission->description);
        $this->assertEquals('Updated Group', $updatedPermission->group);
    }

    /**
     * Test delete permission with safety checks.
     */
    public function test_delete_permission_with_safety_checks(): void
    {
        $permission = Permission::factory()->create();

        $result = $this->rolePermissionService->deletePermission($permission);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }

    /**
     * Test cannot delete permission assigned to roles.
     */
    public function test_cannot_delete_permission_assigned_to_roles(): void
    {
        $permission = Permission::factory()->create();
        $role = Role::factory()->create();
        $role->permissions()->attach($permission);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot delete permission that is assigned to roles.');

        $this->rolePermissionService->deletePermission($permission);
    }

    /**
     * Test user has permission check.
     */
    public function test_user_has_permission(): void
    {
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $role = Role::factory()->create();
        $role->permissions()->attach($this->permission1);
        $user->roles()->attach($role);

        $hasPermission = $this->rolePermissionService->userHasPermission($user, 'manage_users');
        $doesNotHavePermission = $this->rolePermissionService->userHasPermission($user, 'view_reports');

        $this->assertTrue($hasPermission);
        $this->assertFalse($doesNotHavePermission);
    }

    /**
     * Test user has any permission check.
     */
    public function test_user_has_any_permission(): void
    {
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $role = Role::factory()->create();
        $role->permissions()->attach($this->permission1);
        $user->roles()->attach($role);

        $hasAnyPermission = $this->rolePermissionService->userHasAnyPermission($user, ['manage_users', 'view_reports']);
        $doesNotHaveAnyPermission = $this->rolePermissionService->userHasAnyPermission($user, ['view_reports', 'other_permission']);

        $this->assertTrue($hasAnyPermission);
        $this->assertFalse($doesNotHaveAnyPermission);
    }

    /**
     * Test user has all permissions check.
     */
    public function test_user_has_all_permissions(): void
    {
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $role = Role::factory()->create();
        $role->permissions()->attach([$this->permission1->id, $this->permission2->id]);
        $user->roles()->attach($role);

        $hasAllPermissions = $this->rolePermissionService->userHasAllPermissions($user, ['manage_users', 'view_reports']);
        $doesNotHaveAllPermissions = $this->rolePermissionService->userHasAllPermissions($user, ['manage_users', 'other_permission']);

        $this->assertTrue($hasAllPermissions);
        $this->assertFalse($doesNotHaveAllPermissions);
    }

    /**
     * Test get roles with permission.
     */
    public function test_get_roles_with_permission(): void
    {
        $role1 = Role::factory()->create();
        $role1->permissions()->attach($this->permission1);

        $role2 = Role::factory()->create();
        $role2->permissions()->attach($this->permission2);

        $rolesWithPermission = $this->rolePermissionService->getRolesWithPermission('manage_users');

        $this->assertCount(1, $rolesWithPermission);
        $this->assertEquals($role1->id, $rolesWithPermission->first()->id);
    }

    /**
     * Test get users with permission.
     */
    public function test_get_users_with_permission(): void
    {
        $user1 = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $role1 = Role::factory()->create();
        $role1->permissions()->attach($this->permission1);
        $user1->roles()->attach($role1);

        $user2 = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $role2 = Role::factory()->create();
        $role2->permissions()->attach($this->permission2);
        $user2->roles()->attach($role2);

        $usersWithPermission = $this->rolePermissionService->getUsersWithPermission('manage_users');

        $this->assertCount(1, $usersWithPermission);
        $this->assertEquals($user1->id, $usersWithPermission->first()->id);
    }

    /**
     * Test sync role permissions.
     */
    public function test_sync_role_permissions(): void
    {
        $role = Role::factory()->create();
        $role->permissions()->attach($this->permission1);

        // Sync with different permissions
        $this->rolePermissionService->syncRolePermissions($role, [$this->permission2->id]);

        $role->refresh();
        $this->assertFalse($role->hasPermission('manage_users'));
        $this->assertTrue($role->hasPermission('view_reports'));
    }

    /**
     * Test get role statistics.
     */
    public function test_get_role_statistics(): void
    {
        // Create test roles
        Role::factory()->count(3)->create(['is_default' => false]);
        Role::factory()->create(['is_default' => true]);

        // Create role with users
        $roleWithUsers = Role::factory()->create();
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $user->roles()->attach($roleWithUsers);

        $stats = $this->rolePermissionService->getRoleStatistics();

        $this->assertArrayHasKey('total_roles', $stats);
        $this->assertArrayHasKey('default_roles', $stats);
        $this->assertArrayHasKey('roles_with_users', $stats);
        $this->assertArrayHasKey('roles_without_users', $stats);
        $this->assertArrayHasKey('total_permissions', $stats);

        $this->assertGreaterThanOrEqual(4, $stats['total_roles']);
        $this->assertGreaterThanOrEqual(1, $stats['default_roles']);
        $this->assertGreaterThanOrEqual(1, $stats['roles_with_users']);
    }

    /**
     * Test get permission statistics.
     */
    public function test_get_permission_statistics(): void
    {
        $stats = $this->rolePermissionService->getPermissionStatistics();

        $this->assertArrayHasKey('total_permissions', $stats);
        $this->assertArrayHasKey('permissions_by_group', $stats);
        $this->assertArrayHasKey('most_assigned_permissions', $stats);
        $this->assertArrayHasKey('unused_permissions', $stats);

        $this->assertGreaterThanOrEqual(2, $stats['total_permissions']);
    }

    /**
     * Test generate menu items for user.
     */
    public function test_generate_menu_items_for_user(): void
    {
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $role = Role::factory()->create();
        $role->permissions()->attach($this->permission1);
        $user->roles()->attach($role);

        $menuConfig = [
            [
                'title' => 'User Management',
                'permission' => 'manage_users',
                'route' => 'users.index',
            ],
            [
                'title' => 'Reports',
                'permission' => 'view_reports',
                'route' => 'reports.index',
            ],
            [
                'title' => 'Dashboard',
                'route' => 'dashboard',
                // No permission required
            ],
        ];

        $visibleItems = $this->rolePermissionService->getMenuItemsForUser($user, $menuConfig);

        $this->assertCount(2, $visibleItems); // User Management + Dashboard
        $this->assertEquals('User Management', $visibleItems[0]['title']);
        $this->assertEquals('Dashboard', $visibleItems[1]['title']);
    }

    /**
     * Test bulk assign role to users.
     */
    public function test_bulk_assign_role_to_users(): void
    {
        $users = User::factory()->count(3)->create(['tenant_id' => $this->tenant->id]);
        $role = Role::factory()->create();

        $userIds = $users->pluck('id')->toArray();
        $assignedCount = $this->rolePermissionService->bulkAssignRole($userIds, $role->id);

        $this->assertEquals(3, $assignedCount);

        foreach ($users as $user) {
            $this->assertTrue($user->fresh()->roles->contains($role));
        }
    }

    /**
     * Test bulk revoke role from users.
     */
    public function test_bulk_revoke_role_from_users(): void
    {
        $users = User::factory()->count(3)->create(['tenant_id' => $this->tenant->id]);
        $role = Role::factory()->create();

        // Assign role to all users first
        foreach ($users as $user) {
            $user->roles()->attach($role);
        }

        $userIds = $users->pluck('id')->toArray();
        $revokedCount = $this->rolePermissionService->bulkRevokeRole($userIds, $role->id);

        $this->assertEquals(3, $revokedCount);

        foreach ($users as $user) {
            $this->assertFalse($user->fresh()->roles->contains($role));
        }
    }
}
