<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateProjectAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:create-admin {projectCode : The project code} {--username=admin : Admin username} {--password=admin123 : Admin password} {--email=admin@example.com : Admin email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user for a specific project';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $projectCode = $this->argument('projectCode');
        $username = $this->option('username');
        $password = $this->option('password');
        $email = $this->option('email');

        $this->info("Creating admin user for project: {$projectCode}");

        // Set up project database connection
        $databaseName = 'project_'.strtolower($projectCode);

        Config::set('database.connections.project', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $databaseName,
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);

        // Test connection
        try {
            DB::purge('project');
            DB::setDefaultConnection('project');
            $currentDb = DB::select('SELECT DATABASE() as db')[0]->db;
            $this->info("✅ Connected to database: {$currentDb}");
        } catch (\Exception $e) {
            $this->error('❌ Database connection failed: '.$e->getMessage());

            return 1;
        }

        // Check if users table exists
        try {
            $tables = DB::select("SHOW TABLES LIKE 'users'");
            if (count($tables) == 0) {
                $this->error('❌ Users table does not exist in project database');

                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error checking users table: '.$e->getMessage());

            return 1;
        }

        // Create admin user
        try {
            // Check if user already exists
            $existingUser = DB::table('users')->where('username', $username)->first();

            if ($existingUser) {
                $this->warn("⚠️  User '{$username}' already exists. Updating password...");

                DB::table('users')
                    ->where('username', $username)
                    ->update([
                        'password' => Hash::make($password),
                        'level' => 2, // CMS Manager level
                        'role' => 'cms',
                        'updated_at' => now(),
                    ]);

                $this->info("✅ User '{$username}' password updated!");
            } else {
                DB::table('users')->insert([
                    'username' => $username,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'level' => 2, // CMS Manager level
                    'role' => 'cms',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->info('✅ Admin user created successfully!');
            }

            $this->info('📋 Login credentials:');
            $this->info("   URL: /{$projectCode}/login");
            $this->info("   Username: {$username}");
            $this->info("   Password: {$password}");
            $this->info("   Then access: /{$projectCode}/admin/products");

        } catch (\Exception $e) {
            $this->error('❌ Failed to create admin user: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
