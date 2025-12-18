<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User Management
            ['name' => 'manage_users', 'display_name' => 'Manage Users', 'description' => 'Create, edit, delete users', 'group' => 'User Management'],
            ['name' => 'view_users', 'display_name' => 'View Users', 'description' => 'View user list and details', 'group' => 'User Management'],
            ['name' => 'manage_roles', 'display_name' => 'Manage Roles', 'description' => 'Create, edit, delete roles', 'group' => 'User Management'],
            ['name' => 'assign_roles', 'display_name' => 'Assign Roles', 'description' => 'Assign roles to users', 'group' => 'User Management'],
            ['name' => 'view_activity_logs', 'display_name' => 'View Activity Logs', 'description' => 'View user activity logs', 'group' => 'User Management'],

            // Product Management
            ['name' => 'manage_products', 'display_name' => 'Manage Products', 'description' => 'Create, edit, delete products', 'group' => 'Product Management'],
            ['name' => 'view_products', 'display_name' => 'View Products', 'description' => 'View product list and details', 'group' => 'Product Management'],
            ['name' => 'manage_categories', 'display_name' => 'Manage Categories', 'description' => 'Create, edit, delete categories', 'group' => 'Product Management'],
            ['name' => 'manage_brands', 'display_name' => 'Manage Brands', 'description' => 'Create, edit, delete brands', 'group' => 'Product Management'],
            ['name' => 'manage_attributes', 'display_name' => 'Manage Attributes', 'description' => 'Create, edit, delete product attributes', 'group' => 'Product Management'],
            ['name' => 'manage_inventory', 'display_name' => 'Manage Inventory', 'description' => 'Manage product inventory and stock', 'group' => 'Product Management'],
            ['name' => 'import_export_products', 'display_name' => 'Import/Export Products', 'description' => 'Import and export products', 'group' => 'Product Management'],

            // Order Management
            ['name' => 'manage_orders', 'display_name' => 'Manage Orders', 'description' => 'Create, edit, delete orders', 'group' => 'Order Management'],
            ['name' => 'view_orders', 'display_name' => 'View Orders', 'description' => 'View order list and details', 'group' => 'Order Management'],
            ['name' => 'process_payments', 'display_name' => 'Process Payments', 'description' => 'Process order payments', 'group' => 'Order Management'],
            ['name' => 'manage_shipping', 'display_name' => 'Manage Shipping', 'description' => 'Manage order shipping', 'group' => 'Order Management'],
            ['name' => 'generate_invoices', 'display_name' => 'Generate Invoices', 'description' => 'Generate and send invoices', 'group' => 'Order Management'],

            // Content Management
            ['name' => 'manage_posts', 'display_name' => 'Manage Posts', 'description' => 'Create, edit, delete posts', 'group' => 'Content Management'],
            ['name' => 'view_posts', 'display_name' => 'View Posts', 'description' => 'View post list and details', 'group' => 'Content Management'],
            ['name' => 'manage_pages', 'display_name' => 'Manage Pages', 'description' => 'Create, edit, delete pages', 'group' => 'Content Management'],
            ['name' => 'manage_menus', 'display_name' => 'Manage Menus', 'description' => 'Create, edit, delete menus', 'group' => 'Content Management'],
            ['name' => 'manage_translations', 'display_name' => 'Manage Translations', 'description' => 'Manage multi-language content', 'group' => 'Content Management'],

            // Media Management
            ['name' => 'manage_media', 'display_name' => 'Manage Media', 'description' => 'Upload, organize, delete media files', 'group' => 'Media Management'],
            ['name' => 'view_media', 'display_name' => 'View Media', 'description' => 'View media library', 'group' => 'Media Management'],

            // Reports & Analytics
            ['name' => 'view_reports', 'display_name' => 'View Reports', 'description' => 'View system reports', 'group' => 'Reports & Analytics'],
            ['name' => 'create_reports', 'display_name' => 'Create Reports', 'description' => 'Create custom reports', 'group' => 'Reports & Analytics'],
            ['name' => 'export_reports', 'display_name' => 'Export Reports', 'description' => 'Export reports in various formats', 'group' => 'Reports & Analytics'],
            ['name' => 'view_analytics', 'display_name' => 'View Analytics', 'description' => 'View analytics dashboard', 'group' => 'Reports & Analytics'],

            // System Management
            ['name' => 'manage_settings', 'display_name' => 'Manage Settings', 'description' => 'Manage system settings', 'group' => 'System Management'],
            ['name' => 'manage_backups', 'display_name' => 'Manage Backups', 'description' => 'Create and restore backups', 'group' => 'System Management'],
            ['name' => 'view_security_logs', 'display_name' => 'View Security Logs', 'description' => 'View security logs', 'group' => 'System Management'],
            ['name' => 'manage_api', 'display_name' => 'Manage API', 'description' => 'Manage API access and documentation', 'group' => 'System Management'],

            // SEO & Marketing
            ['name' => 'manage_seo', 'display_name' => 'Manage SEO', 'description' => 'Manage SEO settings and optimization', 'group' => 'SEO & Marketing'],
            ['name' => 'manage_newsletters', 'display_name' => 'Manage Newsletters', 'description' => 'Create and send newsletters', 'group' => 'SEO & Marketing'],
            ['name' => 'manage_page_builder', 'display_name' => 'Manage Page Builder', 'description' => 'Use page builder tools', 'group' => 'SEO & Marketing'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Update existing roles with new structure
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'Has access to all system functions',
                'level' => 0,
                'is_default' => false,
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Has access to most system functions',
                'level' => 1,
                'is_default' => false,
            ],
            [
                'name' => 'editor',
                'display_name' => 'Editor',
                'description' => 'Can manage content and products',
                'level' => 2,
                'is_default' => true,
            ],
            [
                'name' => 'support',
                'display_name' => 'Support',
                'description' => 'Limited access for customer support',
                'level' => 3,
                'is_default' => false,
            ],
            [
                'name' => 'viewer',
                'display_name' => 'Viewer',
                'description' => 'Read-only access to most content',
                'level' => 4,
                'is_default' => false,
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );

            // Assign permissions to roles
            $this->assignPermissionsToRole($role);
        }
    }

    private function assignPermissionsToRole(Role $role): void
    {
        switch ($role->name) {
            case 'super_admin':
                // Super admin gets all permissions
                $role->permissions()->sync(Permission::all()->pluck('id'));
                break;

            case 'admin':
                // Admin gets most permissions except super admin specific ones
                $permissions = Permission::whereNotIn('name', [
                    'manage_backups',
                    'view_security_logs',
                ])->pluck('id');
                $role->permissions()->sync($permissions);
                break;

            case 'editor':
                // Editor gets content and product management permissions
                $permissions = Permission::whereIn('name', [
                    'manage_products', 'view_products', 'manage_categories', 'manage_brands',
                    'manage_attributes', 'manage_inventory', 'import_export_products',
                    'manage_posts', 'view_posts', 'manage_pages', 'manage_menus',
                    'manage_translations', 'manage_media', 'view_media',
                    'view_orders', 'manage_seo', 'manage_page_builder',
                ])->pluck('id');
                $role->permissions()->sync($permissions);
                break;

            case 'support':
                // Support gets limited view permissions
                $permissions = Permission::whereIn('name', [
                    'view_products', 'view_orders', 'view_posts', 'view_media',
                    'view_users', 'view_reports',
                ])->pluck('id');
                $role->permissions()->sync($permissions);
                break;

            case 'viewer':
                // Viewer gets only view permissions
                $permissions = Permission::whereIn('name', [
                    'view_products', 'view_posts', 'view_media', 'view_reports',
                ])->pluck('id');
                $role->permissions()->sync($permissions);
                break;
        }
    }
}
