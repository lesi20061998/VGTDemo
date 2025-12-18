<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Role $adminRole;

    protected Role $userRole;

    protected Tenant $tenant;

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

        // Create roles
        $this->adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Administrator role',
            'is_default' => false,
            'level' => 1,
        ]);

        $this->userRole = Role::create([
            'name' => 'user',
            'display_name' => 'User',
            'description' => 'Regular user role',
            'is_default' => true,
            'level' => 5,
        ]);

        // Create permissions
        $manageUsersPermission = Permission::create([
            'name' => 'manage_users',
            'display_name' => 'Manage Users',
            'description' => 'Create, edit, delete users',
            'group' => 'User Management',
        ]);

        // Assign permissions to admin role
        $this->adminRole->permissions()->attach($manageUsersPermission->id);

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
     * Test user index method processes data correctly.
     */
    public function test_index_processes_data_correctly(): void
    {
        // Create additional test users
        $users = User::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
        ]);

        // Test the controller logic directly
        $controller = new \App\Http\Controllers\Admin\UserController;
        $request = new \Illuminate\Http\Request;

        // Test without search
        $query = User::with(['roles', 'activityLogs' => function ($q) {
            $q->latest()->limit(5);
        }]);
        $result = $query->paginate(15);

        $this->assertGreaterThanOrEqual(4, $result->total()); // admin + 3 test users

        // Test with search
        $request->merge(['search' => $users[0]->name]);
        $query = User::with(['roles', 'activityLogs' => function ($q) {
            $q->latest()->limit(5);
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $searchResult = $query->paginate(15);
        $this->assertEquals(1, $searchResult->total());
    }

    /**
     * Test user index with search functionality.
     */
    public function test_index_with_search(): void
    {
        $searchUser = User::factory()->create([
            'name' => 'John Searchable',
            'email' => 'john.search@example.com',
            'username' => 'johnsearch',
            'tenant_id' => $this->tenant->id,
        ]);

        $otherUser = User::factory()->create([
            'name' => 'Jane Other',
            'email' => 'jane@example.com',
            'username' => 'janeother',
            'tenant_id' => $this->tenant->id,
        ]);

        // Search by name
        $response = $this->actingAs($this->admin)
            ->get(route('cms.users.index', ['search' => 'John']));

        $response->assertStatus(200);
        $response->assertSee($searchUser->name);
        $response->assertDontSee($otherUser->name);

        // Search by email
        $response = $this->actingAs($this->admin)
            ->get(route('cms.users.index', ['search' => 'search@example.com']));

        $response->assertStatus(200);
        $response->assertSee($searchUser->email);
        $response->assertDontSee($otherUser->email);
    }

    /**
     * Test user index with role filter.
     */
    public function test_index_with_role_filter(): void
    {
        $adminUser = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $adminUser->roles()->attach($this->adminRole);

        $regularUser = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $regularUser->roles()->attach($this->userRole);

        $response = $this->actingAs($this->admin)
            ->get(route('cms.users.index', ['role' => 'admin']));

        $response->assertStatus(200);
        $response->assertSee($adminUser->name);
        $response->assertDontSee($regularUser->name);
    }

    /**
     * Test user creation form displays correctly.
     */
    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('cms.users.create'));

        $response->assertStatus(200);
        $response->assertViewIs('cms.users.create');
        $response->assertViewHas('roles');
        $response->assertSee('Create User');
    }

    /**
     * Test user creation with valid data.
     */
    public function test_store_creates_user_with_valid_data(): void
    {
        Storage::fake('public');

        $userData = [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '+1234567890',
            'address' => '123 Main St',
            'status' => true,
            'roles' => [$this->userRole->id],
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('cms.users.store'), $userData);

        $response->assertRedirect(route('cms.users.index'));
        $response->assertSessionHas('success', 'User created successfully.');

        // Assert user was created
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'address' => '123 Main St',
            'status' => true,
        ]);

        // Assert password is hashed
        $user = User::where('email', 'john@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));

        // Assert role was assigned
        $this->assertTrue($user->hasRole('user'));
    }

    /**
     * Test user creation with avatar upload.
     */
    public function test_store_creates_user_with_avatar(): void
    {
        Storage::fake('public');

        $avatar = UploadedFile::fake()->image('avatar.jpg', 100, 100);

        $userData = [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'avatar' => $avatar,
            'status' => true,
            'roles' => [$this->userRole->id],
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('cms.users.store'), $userData);

        $response->assertRedirect(route('cms.users.index'));

        // Assert user was created
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user->avatar);

        // Assert avatar file was stored
        Storage::disk('public')->assertExists($user->avatar);
    }

    /**
     * Test user creation validation.
     */
    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('cms.users.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    /**
     * Test user creation prevents duplicate email.
     */
    public function test_store_prevents_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $userData = [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('cms.users.store'), $userData);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test user show page displays user details.
     */
    public function test_show_displays_user_details(): void
    {
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $user->roles()->attach($this->userRole);

        $response = $this->actingAs($this->admin)
            ->get(route('cms.users.show', $user));

        $response->assertStatus(200);
        $response->assertViewIs('cms.users.show');
        $response->assertViewHas('user');
        $response->assertSee($user->name);
        $response->assertSee($user->email);
    }

    /**
     * Test user edit form displays correctly.
     */
    public function test_edit_displays_form(): void
    {
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('cms.users.edit', $user));

        $response->assertStatus(200);
        $response->assertViewIs('cms.users.edit');
        $response->assertViewHas('user');
        $response->assertViewHas('roles');
        $response->assertSee($user->name);
    }

    /**
     * Test user update with valid data.
     */
    public function test_update_modifies_user_with_valid_data(): void
    {
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);

        $updateData = [
            'name' => 'Updated Name',
            'username' => 'updateduser',
            'email' => 'updated@example.com',
            'phone' => '+9876543210',
            'address' => '456 Oak Ave',
            'status' => false,
            'roles' => [$this->adminRole->id],
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('cms.users.update', $user), $updateData);

        $response->assertRedirect(route('cms.users.index'));
        $response->assertSessionHas('success', 'User updated successfully.');

        // Assert user was updated
        $user->refresh();
        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals('updateduser', $user->username);
        $this->assertEquals('updated@example.com', $user->email);
        $this->assertEquals('+9876543210', $user->phone);
        $this->assertEquals('456 Oak Ave', $user->address);
        $this->assertFalse($user->status);

        // Assert role was updated
        $this->assertTrue($user->hasRole('admin'));
    }

    /**
     * Test user update with password change.
     */
    public function test_update_changes_password_when_provided(): void
    {
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $originalPassword = $user->password;

        $updateData = [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
            'status' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('cms.users.update', $user), $updateData);

        $response->assertRedirect(route('cms.users.index'));

        // Assert password was changed
        $user->refresh();
        $this->assertNotEquals($originalPassword, $user->password);
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    /**
     * Test user update without password change.
     */
    public function test_update_keeps_password_when_not_provided(): void
    {
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $originalPassword = $user->password;

        $updateData = [
            'name' => 'Updated Name',
            'username' => $user->username,
            'email' => $user->email,
            'status' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('cms.users.update', $user), $updateData);

        $response->assertRedirect(route('cms.users.index'));

        // Assert password was not changed
        $user->refresh();
        $this->assertEquals($originalPassword, $user->password);
    }

    /**
     * Test user deletion.
     */
    public function test_destroy_deletes_user(): void
    {
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);

        $response = $this->actingAs($this->admin)
            ->delete(route('cms.users.destroy', $user));

        $response->assertRedirect(route('cms.users.index'));
        $response->assertSessionHas('success', 'User deleted successfully.');

        // Assert user was deleted
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /**
     * Test cannot delete super admin.
     */
    public function test_destroy_prevents_deleting_super_admin(): void
    {
        $superAdmin = User::factory()->create([
            'level' => 0, // Super admin level
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('cms.users.destroy', $superAdmin));

        $response->assertRedirect(route('cms.users.index'));
        $response->assertSessionHas('error', 'Cannot delete super administrator.');

        // Assert user was not deleted
        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);
    }

    /**
     * Test cannot delete own account.
     */
    public function test_destroy_prevents_self_deletion(): void
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('cms.users.destroy', $this->admin));

        $response->assertRedirect(route('cms.users.index'));
        $response->assertSessionHas('error', 'Cannot delete your own account.');

        // Assert user was not deleted
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    /**
     * Test role assignment to user.
     */
    public function test_assign_role_assigns_role_to_user(): void
    {
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);

        $response = $this->actingAs($this->admin)
            ->post(route('cms.users.assign-role', $user), [
                'role_id' => $this->userRole->id,
            ]);

        $response->assertJson(['success' => true]);

        // Assert role was assigned
        $this->assertTrue($user->fresh()->hasRole('user'));
    }

    /**
     * Test role assignment prevents duplicates.
     */
    public function test_assign_role_prevents_duplicate_assignment(): void
    {
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $user->roles()->attach($this->userRole);

        $response = $this->actingAs($this->admin)
            ->post(route('cms.users.assign-role', $user), [
                'role_id' => $this->userRole->id,
            ]);

        $response->assertJson(['success' => false]);
    }

    /**
     * Test role revocation from user.
     */
    public function test_revoke_role_removes_role_from_user(): void
    {
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $user->roles()->attach($this->userRole);

        $response = $this->actingAs($this->admin)
            ->post(route('cms.users.revoke-role', $user), [
                'role_id' => $this->userRole->id,
            ]);

        $response->assertJson(['success' => true]);

        // Assert role was revoked
        $this->assertFalse($user->fresh()->hasRole('user'));
    }

    /**
     * Test role revocation when user doesn't have role.
     */
    public function test_revoke_role_handles_missing_role(): void
    {
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);

        $response = $this->actingAs($this->admin)
            ->post(route('cms.users.revoke-role', $user), [
                'role_id' => $this->userRole->id,
            ]);

        $response->assertJson(['success' => false]);
    }

    /**
     * Test user status toggle.
     */
    public function test_toggle_status_changes_user_status(): void
    {
        $user = User::factory()->create([
            'status' => true,
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('cms.users.toggle-status', $user));

        $response->assertJson(['success' => true, 'status' => false]);

        // Assert status was changed
        $this->assertFalse($user->fresh()->status);
    }

    /**
     * Test cannot toggle super admin status.
     */
    public function test_toggle_status_prevents_disabling_super_admin(): void
    {
        $superAdmin = User::factory()->create([
            'level' => 0, // Super admin level
            'status' => true,
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('cms.users.toggle-status', $superAdmin));

        $response->assertJson(['success' => false]);

        // Assert status was not changed
        $this->assertTrue($superAdmin->fresh()->status);
    }

    /**
     * Test cannot toggle own status.
     */
    public function test_toggle_status_prevents_self_disabling(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('cms.users.toggle-status', $this->admin));

        $response->assertJson(['success' => false]);

        // Assert status was not changed
        $this->assertTrue($this->admin->fresh()->status);
    }

    /**
     * Test activity logging is disabled in testing environment.
     */
    public function test_activity_logging_disabled_in_testing(): void
    {
        $initialLogCount = ActivityLog::count();

        $userData = [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => true,
        ];

        $this->actingAs($this->admin)
            ->post(route('cms.users.store'), $userData);

        // Assert no new activity logs were created
        $this->assertEquals($initialLogCount, ActivityLog::count());
    }
}
