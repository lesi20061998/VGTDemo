<?php

namespace Tests\Unit\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UserService $userService;

    protected Tenant $tenant;

    protected Role $adminRole;

    protected Role $userRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = new UserService;

        // Create a tenant first
        $this->tenant = Tenant::create([
            'name' => 'Test Tenant',
            'code' => 'test',
            'domain' => 'test.example.com',
            'database_name' => 'test_db',
            'status' => 'active',
        ]);

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
    }

    /**
     * Test user creation with complete information.
     */
    public function test_create_user_with_complete_information(): void
    {
        $userData = [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'phone' => '+1234567890',
            'address' => '123 Main St',
            'status' => true,
            'tenant_id' => $this->tenant->id,
            'roles' => [$this->userRole->id],
        ];

        $user = $this->userService->createUser($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('johndoe', $user->username);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('+1234567890', $user->phone);
        $this->assertEquals('123 Main St', $user->address);
        $this->assertTrue($user->status);
        $this->assertEquals($this->tenant->id, $user->tenant_id);

        // Assert password is hashed
        $this->assertTrue(Hash::check('password123', $user->password));

        // Assert role was assigned
        $this->assertTrue($user->hasRole('user'));
    }

    /**
     * Test user creation with avatar upload.
     */
    public function test_create_user_with_avatar(): void
    {
        Storage::fake('public');

        $avatar = UploadedFile::fake()->image('avatar.jpg', 100, 100);

        $userData = [
            'name' => 'Jane Doe',
            'username' => 'janedoe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'avatar' => $avatar,
            'status' => true,
            'tenant_id' => $this->tenant->id,
        ];

        $user = $this->userService->createUser($userData);

        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    /**
     * Test user update functionality.
     */
    public function test_update_user(): void
    {
        $user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '+9876543210',
            'status' => false,
            'roles' => [$this->adminRole->id],
        ];

        $updatedUser = $this->userService->updateUser($user, $updateData);

        $this->assertEquals('Updated Name', $updatedUser->name);
        $this->assertEquals('updated@example.com', $updatedUser->email);
        $this->assertEquals('+9876543210', $updatedUser->phone);
        $this->assertFalse($updatedUser->status);
        $this->assertTrue($updatedUser->hasRole('admin'));
    }

    /**
     * Test user deletion with safety checks.
     */
    public function test_delete_user_with_safety_checks(): void
    {
        $user = User::factory()->create([
            'level' => 2, // Regular user
            'tenant_id' => $this->tenant->id,
        ]);

        $result = $this->userService->deleteUser($user);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /**
     * Test cannot delete super admin.
     */
    public function test_cannot_delete_super_admin(): void
    {
        $superAdmin = User::factory()->create([
            'level' => 0, // Super admin level
            'tenant_id' => $this->tenant->id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot delete super administrator.');

        $this->userService->deleteUser($superAdmin);
    }

    /**
     * Test role assignment to user.
     */
    public function test_assign_role_to_user(): void
    {
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);

        $result = $this->userService->assignRoleToUser($user, $this->userRole->id);

        $this->assertTrue($result);
        $this->assertTrue($user->fresh()->hasRole('user'));
    }

    /**
     * Test role assignment prevents duplicates.
     */
    public function test_assign_role_prevents_duplicates(): void
    {
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $user->roles()->attach($this->userRole);

        $result = $this->userService->assignRoleToUser($user, $this->userRole->id);

        $this->assertFalse($result);
    }

    /**
     * Test role revocation from user.
     */
    public function test_revoke_role_from_user(): void
    {
        $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $user->roles()->attach($this->userRole);

        $result = $this->userService->revokeRoleFromUser($user, $this->userRole->id);

        $this->assertTrue($result);
        $this->assertFalse($user->fresh()->hasRole('user'));
    }

    /**
     * Test user status toggle.
     */
    public function test_toggle_user_status(): void
    {
        $user = User::factory()->create([
            'status' => true,
            'level' => 2, // Regular user
            'tenant_id' => $this->tenant->id,
        ]);

        $newStatus = $this->userService->toggleUserStatus($user);

        $this->assertFalse($newStatus);
        $this->assertFalse($user->fresh()->status);
    }

    /**
     * Test cannot toggle super admin status.
     */
    public function test_cannot_toggle_super_admin_status(): void
    {
        $superAdmin = User::factory()->create([
            'level' => 0, // Super admin level
            'status' => true,
            'tenant_id' => $this->tenant->id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot disable super administrator.');

        $this->userService->toggleUserStatus($superAdmin);
    }

    /**
     * Test get users by role.
     */
    public function test_get_users_by_role(): void
    {
        $adminUser = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $adminUser->roles()->attach($this->adminRole);

        $regularUser = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $regularUser->roles()->attach($this->userRole);

        $adminUsers = $this->userService->getUsersByRole('admin');
        $regularUsers = $this->userService->getUsersByRole('user');

        $this->assertCount(1, $adminUsers);
        $this->assertCount(1, $regularUsers);
        $this->assertEquals($adminUser->id, $adminUsers->first()->id);
        $this->assertEquals($regularUser->id, $regularUsers->first()->id);
    }

    /**
     * Test get users with specific permission.
     */
    public function test_get_users_with_permission(): void
    {
        // Create permission
        $permission = Permission::create([
            'name' => 'test_permission',
            'display_name' => 'Test Permission',
            'description' => 'A test permission',
            'group' => 'Test',
        ]);

        // Assign permission to admin role
        $this->adminRole->permissions()->attach($permission);

        // Create users with different roles
        $adminUser = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $adminUser->roles()->attach($this->adminRole);

        $regularUser = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $regularUser->roles()->attach($this->userRole);

        $usersWithPermission = $this->userService->getUsersWithPermission('test_permission');

        $this->assertCount(1, $usersWithPermission);
        $this->assertEquals($adminUser->id, $usersWithPermission->first()->id);
    }

    /**
     * Test assign user to project.
     */
    public function test_assign_user_to_project(): void
    {
        $user = User::factory()->create([
            'project_ids' => null,
            'tenant_id' => $this->tenant->id,
        ]);

        $this->userService->assignUserToProject($user, 123);

        $user->refresh();
        $this->assertContains(123, $user->project_ids);
    }

    /**
     * Test remove user from project.
     */
    public function test_remove_user_from_project(): void
    {
        $user = User::factory()->create([
            'project_ids' => [123, 456],
            'tenant_id' => $this->tenant->id,
        ]);

        $this->userService->removeUserFromProject($user, 123);

        $user->refresh();
        $this->assertNotContains(123, $user->project_ids);
        $this->assertContains(456, $user->project_ids);
    }

    /**
     * Test update user preferences.
     */
    public function test_update_user_preferences(): void
    {
        $user = User::factory()->create([
            'preferences' => ['theme' => 'dark'],
            'tenant_id' => $this->tenant->id,
        ]);

        $this->userService->updateUserPreferences($user, ['language' => 'en']);

        $user->refresh();
        $this->assertEquals('dark', $user->preferences['theme']);
        $this->assertEquals('en', $user->preferences['language']);
    }

    /**
     * Test get user statistics.
     */
    public function test_get_user_statistics(): void
    {
        // Create test users
        User::factory()->count(3)->create([
            'status' => true,
            'level' => 2,
            'tenant_id' => $this->tenant->id,
        ]);

        User::factory()->count(2)->create([
            'status' => false,
            'level' => 2,
            'tenant_id' => $this->tenant->id,
        ]);

        $stats = $this->userService->getUserStatistics();

        $this->assertArrayHasKey('total_users', $stats);
        $this->assertArrayHasKey('active_users', $stats);
        $this->assertArrayHasKey('inactive_users', $stats);
        $this->assertArrayHasKey('super_admins', $stats);
        $this->assertArrayHasKey('administrators', $stats);
        $this->assertArrayHasKey('regular_users', $stats);

        $this->assertGreaterThanOrEqual(5, $stats['total_users']);
        $this->assertGreaterThanOrEqual(3, $stats['active_users']);
        $this->assertGreaterThanOrEqual(2, $stats['inactive_users']);
    }

    /**
     * Test search users with criteria.
     */
    public function test_search_users_with_criteria(): void
    {
        $user1 = User::factory()->create([
            'name' => 'John Smith',
            'email' => 'john@example.com',
            'status' => true,
            'level' => 2,
            'tenant_id' => $this->tenant->id,
        ]);

        $user2 = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'status' => false,
            'level' => 3,
            'tenant_id' => $this->tenant->id,
        ]);

        // Search by name
        $results = $this->userService->searchUsers(['name' => 'John']);
        $this->assertCount(1, $results);
        $this->assertEquals($user1->id, $results->first()->id);

        // Search by status
        $results = $this->userService->searchUsers(['status' => false]);
        $this->assertCount(1, $results);
        $this->assertEquals($user2->id, $results->first()->id);

        // Search by level
        $results = $this->userService->searchUsers(['level' => 3]);
        $this->assertCount(1, $results);
        $this->assertEquals($user2->id, $results->first()->id);
    }
}
