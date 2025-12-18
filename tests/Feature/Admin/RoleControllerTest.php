<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Role $adminRole;

    protected Tenant $tenant;

    protected Permission $permission1;

    protected Permission $permission2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a tenant first
        $this->tenant = Tenant::create([
            'name' => 'Test Tenant',
            'code' => 'test',
            'domain' => 'test.example.com',
            'database_name' => 'test_db',
            'status' => 'active',
        ]);

        // Set default tenant ID for testing
        config(['app.default_tenant_id' => $this->tenant->id]);
        session(['current_tenant_id' => $this->tenant->id]);

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

        // Create admin role
        $this->adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Administrator role',
            'is_default' => false,
            'level' => 1,
        ]);

        // Assign permissions to admin role
        $this->adminRole->permissions()->attach([$this->permission1->id, $this->permission2->id]);

        // Create admin user
        $this->admin = User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'level' => 1,
            'status' => true,
            'tenant_id' => $this->tenant->id,
        ]);

        // Assign admin role
        $this->admin->roles()->attach($this->adminRole);
    }

    /**
     * Test role index page displays roles correctly.
     */
    public function test_index_displays_roles(): void
    {
        // Create additional test roles
        $roles = Role::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('cms.roles.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cms.roles.index');
        $response->assertViewHas('roles');

        // Check that roles are displayed
        foreach ($roles as $role) {
            $response->assertSee($role->display_name);
            $response->assertSee($role->name);
        }
    }

    /**
     * Test role index with search functionality.
     */
    public function test_index_with_search(): void
    {
        $searchRole = Role::create([
            'name' => 'searchable_role',
            'display_name' => 'Searchable Role',
            'description' => 'A role for searching',
            'level' => 3,
        ]);

        $otherRole = Role::create([
            'name' => 'other_role',
            'display_name' => 'Other Role',
            'description' => 'Another role',
            'level' => 4,
        ]);

        // Search by name
        $response = $this->actingAs($this->admin)
            ->get(route('cms.roles.index', ['search' => 'searchable']));

        $response->assertStatus(200);
        $response->assertSee($searchRole->display_name);
        $response->assertDontSee($otherRole->display_name);

        // Search by display name
        $response = $this->actingAs($this->admin)
            ->get(route('cms.roles.index', ['search' => 'Searchable Role']));

        $response->assertStatus(200);
        $response->assertSee($searchRole->display_name);
        $response->assertDontSee($otherRole->display_name);
    }

    /**
     * Test role index with level filter.
     */
    public function test_index_with_level_filter(): void
    {
        $level3Role = Role::create([
            'name' => 'level3_role',
            'display_name' => 'Level 3 Role',
            'description' => 'Level 3 role',
            'level' => 3,
        ]);

        $level5Role = Role::create([
            'name' => 'level5_role',
            'display_name' => 'Level 5 Role',
            'description' => 'Level 5 role',
            'level' => 5,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('cms.roles.index', ['level' => 3]));

        $response->assertStatus(200);
        $response->assertSee($level3Role->display_name);
        $response->assertDontSee($level5Role->display_name);
    }

    /**
     * Test role creation form displays correctly.
     */
    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('cms.roles.create'));

        $response->assertStatus(200);
        $response->assertViewIs('cms.roles.create');
        $response->assertViewHas('permissions');
        $response->assertSee('Create Role');
    }

    /**
     * Test role creation with valid data.
     */
    public function test_store_creates_role_with_valid_data(): void
    {
        $roleData = [
            'name' => 'test_role',
            'display_name' => 'Test Role',
            'description' => 'A test role',
            'level' => 3,
            'is_default' => false,
            'permissions' => [$this->permission1->id],
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('cms.roles.store'), $roleData);

        $response->assertRedirect(route('cms.roles.index'));
        $response->assertSessionHas('success', 'Role created successfully.');

        // Assert role was created
        $this->assertDatabaseHas('roles', [
            'name' => 'test_role',
            'display_name' => 'Test Role',
            'description' => 'A test role',
            'level' => 3,
            'is_default' => false,
        ]);

        // Assert permission was assigned
        $role = Role::where('name', 'test_role')->first();
        $this->assertTrue($role->hasPermission('manage_users'));
    }

    /**
     * Test role creation with default flag.
     */
    public function test_store_creates_default_role(): void
    {
        $roleData = [
            'name' => 'default_role',
            'display_name' => 'Default Role',
            'description' => 'A default role',
            'level' => 5,
            'is_default' => true,
            'permissions' => [],
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('cms.roles.store'), $roleData);

        $response->assertRedirect(route('cms.roles.index'));

        // Assert role was created as default
        $this->assertDatabaseHas('roles', [
            'name' => 'default_role',
            'is_default' => true,
        ]);
    }

    /**
     * Test role creation validation.
     */
    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('cms.roles.store'), []);

        $response->assertSessionHasErrors(['name', 'display_name', 'level']);
    }

    /**
     * Test role creation prevents duplicate name.
     */
    public function test_store_prevents_duplicate_name(): void
    {
        Role::create([
            'name' => 'existing_role',
            'display_name' => 'Existing Role',
            'level' => 3,
        ]);

        $roleData = [
            'name' => 'existing_role',
            'display_name' => 'Another Role',
            'level' => 4,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('cms.roles.store'), $roleData);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * Test role show page displays role details.
     */
    public function test_show_displays_role_details(): void
    {
        $role = Role::factory()->create();
        $role->permissions()->attach($this->permission1);

        $response = $this->actingAs($this->admin)
            ->get(route('cms.roles.show', $role));

        $response->assertStatus(200);
        $response->assertViewIs('cms.roles.show');
        $response->assertViewHas('role');
        $response->assertSee($role->display_name);
        $response->assertSee($role->description);
    }

    /**
     * Test role edit form displays correctly.
     */
    public function test_edit_displays_form(): void
    {
        $role = Role::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('cms.roles.edit', $role));

        $response->assertStatus(200);
        $response->assertViewIs('cms.roles.edit');
        $response->assertViewHas('role');
        $response->assertViewHas('permissions');
        $response->assertSee($role->display_name);
    }

    /**
     * Test role update with valid data.
     */
    public function test_update_modifies_role_with_valid_data(): void
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

        $response = $this->actingAs($this->admin)
            ->put(route('cms.roles.update', $role), $updateData);

        $response->assertRedirect(route('cms.roles.index'));
        $response->assertSessionHas('success', 'Role updated successfully.');

        // Assert role was updated
        $role->refresh();
        $this->assertEquals('updated_role', $role->name);
        $this->assertEquals('Updated Role', $role->display_name);
        $this->assertEquals('Updated description', $role->description);
        $this->assertEquals(4, $role->level);
        $this->assertTrue($role->is_default);

        // Assert permission was updated
        $this->assertTrue($role->hasPermission('view_reports'));
        $this->assertFalse($role->hasPermission('manage_users'));
    }

    /**
     * Test role update validation prevents duplicate name.
     */
    public function test_update_prevents_duplicate_name(): void
    {
        $existingRole = Role::factory()->create(['name' => 'existing_role']);
        $role = Role::factory()->create(['name' => 'test_role']);

        $updateData = [
            'name' => 'existing_role',
            'display_name' => 'Test Role',
            'level' => 3,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('cms.roles.update', $role), $updateData);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * Test role deletion.
     */
    public function test_destroy_deletes_role(): void
    {
        $role = Role::factory()->create(['is_default' => false]);

        $response = $this->actingAs($this->admin)
            ->delete(route('cms.roles.destroy', $role));

        $response->assertRedirect(route('cms.roles.index'));
        $response->assertSessionHas('success', 'Role deleted successfully.');

        // Assert role was deleted
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    /**
     * Test cannot delete default role.
     */
    public function test_destroy_prevents_deleting_default_role(): void
    {
        $defaultRole = Role::factory()->create(['is_default' => true]);

        $response = $this->actingAs($this->admin)
            ->delete(route('cms.roles.destroy', $defaultRole));

        $response->assertRedirect(route('cms.roles.index'));
        $response->assertSessionHas('error', 'Cannot delete default role.');

        // Assert role was not deleted
        $this->assertDatabaseHas('roles', ['id' => $defaultRole->id]);
    }

    /**
     * Test cannot delete role with assigned users.
     */
    public function test_destroy_prevents_deleting_role_with_users(): void
    {
        $role = Role::factory()->create(['is_default' => false]);
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $user->roles()->attach($role);

        $response = $this->actingAs($this->admin)
            ->delete(route('cms.roles.destroy', $role));

        $response->assertRedirect(route('cms.roles.index'));
        $response->assertSessionHas('error', 'Cannot delete role that has assigned users.');

        // Assert role was not deleted
        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    /**
     * Test permission assignment to role.
     */
    public function test_assign_permission_assigns_permission_to_role(): void
    {
        $role = Role::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post(route('cms.roles.assign-permission', $role), [
                'permission_id' => $this->permission1->id,
            ]);

        $response->assertJson(['success' => true]);

        // Assert permission was assigned
        $this->assertTrue($role->fresh()->hasPermission('manage_users'));
    }

    /**
     * Test permission assignment prevents duplicates.
     */
    public function test_assign_permission_prevents_duplicate_assignment(): void
    {
        $role = Role::factory()->create();
        $role->permissions()->attach($this->permission1);

        $response = $this->actingAs($this->admin)
            ->post(route('cms.roles.assign-permission', $role), [
                'permission_id' => $this->permission1->id,
            ]);

        $response->assertJson(['success' => false]);
    }

    /**
     * Test permission revocation from role.
     */
    public function test_revoke_permission_removes_permission_from_role(): void
    {
        $role = Role::factory()->create();
        $role->permissions()->attach($this->permission1);

        $response = $this->actingAs($this->admin)
            ->post(route('cms.roles.revoke-permission', $role), [
                'permission_id' => $this->permission1->id,
            ]);

        $response->assertJson(['success' => true]);

        // Assert permission was revoked
        $this->assertFalse($role->fresh()->hasPermission('manage_users'));
    }

    /**
     * Test permission revocation when role doesn't have permission.
     */
    public function test_revoke_permission_handles_missing_permission(): void
    {
        $role = Role::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post(route('cms.roles.revoke-permission', $role), [
                'permission_id' => $this->permission1->id,
            ]);

        $response->assertJson(['success' => false]);
    }

    /**
     * Test setting role as default.
     */
    public function test_set_default_sets_role_as_default(): void
    {
        // Create existing default role
        $existingDefault = Role::factory()->create(['is_default' => true]);

        // Create role to set as default
        $role = Role::factory()->create(['is_default' => false]);

        $response = $this->actingAs($this->admin)
            ->post(route('cms.roles.set-default', $role));

        $response->assertJson(['success' => true]);

        // Assert role was set as default
        $this->assertTrue($role->fresh()->is_default);

        // Assert previous default role is no longer default
        $this->assertFalse($existingDefault->fresh()->is_default);
    }

    /**
     * Test role creation with multiple permissions.
     */
    public function test_store_creates_role_with_multiple_permissions(): void
    {
        $roleData = [
            'name' => 'multi_perm_role',
            'display_name' => 'Multi Permission Role',
            'description' => 'Role with multiple permissions',
            'level' => 3,
            'is_default' => false,
            'permissions' => [$this->permission1->id, $this->permission2->id],
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('cms.roles.store'), $roleData);

        $response->assertRedirect(route('cms.roles.index'));

        // Assert role was created with both permissions
        $role = Role::where('name', 'multi_perm_role')->first();
        $this->assertTrue($role->hasPermission('manage_users'));
        $this->assertTrue($role->hasPermission('view_reports'));
    }

    /**
     * Test role update removes permissions when not included.
     */
    public function test_update_removes_permissions_when_not_included(): void
    {
        $role = Role::factory()->create();
        $role->permissions()->attach([$this->permission1->id, $this->permission2->id]);

        $updateData = [
            'name' => $role->name,
            'display_name' => $role->display_name,
            'level' => $role->level,
            'permissions' => [$this->permission1->id], // Only include one permission
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('cms.roles.update', $role), $updateData);

        $response->assertRedirect(route('cms.roles.index'));

        // Assert only one permission remains
        $role->refresh();
        $this->assertTrue($role->hasPermission('manage_users'));
        $this->assertFalse($role->hasPermission('view_reports'));
    }

    /**
     * Test activity logging is disabled in testing environment.
     */
    public function test_activity_logging_disabled_in_testing(): void
    {
        $initialLogCount = ActivityLog::count();

        $roleData = [
            'name' => 'test_role',
            'display_name' => 'Test Role',
            'description' => 'A test role',
            'level' => 3,
            'is_default' => false,
        ];

        $this->actingAs($this->admin)
            ->post(route('cms.roles.store'), $roleData);

        // Assert no new activity logs were created
        $this->assertEquals($initialLogCount, ActivityLog::count());
    }
}
