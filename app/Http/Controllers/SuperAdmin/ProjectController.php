<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectSetting;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['contract', 'admin', 'createdBy'])->latest()->get();

        return view('superadmin.projects.index', compact('projects'));
    }

    public function create()
    {
        // Only get approved contracts that don't have a project yet
        $contracts = Contract::where('status', 'approved')
            ->whereDoesntHave('projects')
            ->with('employee')
            ->get();
        $employees = Employee::where('is_active', true)->get();

        return view('superadmin.projects.create', compact('contracts', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'employee_id' => 'required|exists:employees,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:projects,code',
            'client_name' => 'nullable|string|max:255',
        ]);

        $contract = Contract::findOrFail($request->contract_id);
        $employee = Employee::findOrFail($request->employee_id);

        $baseUrl = config('app.url');
        $subdomain = $baseUrl.'/'.$contract->contract_code;

        $project = Project::create([
            'contract_id' => $request->contract_id,
            'name' => $request->name,
            'code' => $request->code,
            'subdomain' => $subdomain,
            'client_name' => $request->client_name ?? $contract->client_name ?? 'TBD',
            'start_date' => now(),
            'deadline' => $contract->deadline ?? now()->addMonth(),
            'technical_requirements' => $contract->requirements,
            'features' => $contract->design_description,
            'notes' => $request->notes,
            'admin_id' => $request->employee_id,
            'created_by' => $contract->employee_id,
            'status' => 'assigned',
        ]);

        return redirect()->route('superadmin.projects.show', $project)->with('alert', [
            'type' => 'success',
            'message' => 'Tạo và phân phối dự án thành công!',
        ]);
    }

    public function show(Project $project)
    {
        $project->load(['contract', 'admin', 'createdBy']);

        return view('superadmin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $contracts = Contract::where('status', 'approved')->with('employee')->get();
        $employees = Employee::where('is_active', true)->get();

        return view('superadmin.projects.edit', compact('project', 'contracts', 'employees'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'notes' => 'nullable|string',
        ]);

        $project->update($request->only(['name', 'subdomain', 'employee_ids', 'notes']));

        return redirect()->route('superadmin.projects.show', $project)->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật dự án thành công!',
        ]);
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('superadmin.projects.index')->with('alert', [
            'type' => 'success',
            'message' => 'Xóa dự án thành công!',
        ]);
    }

    public function createWebsite(Project $project)
    {
        if ($project->status !== 'assigned') {
            return back()->with('alert', [
                'type' => 'error',
                'message' => 'Dự án chưa được phân phối!',
            ]);
        }

        try {
            // Check if multisite mode is enabled
            if (env('MULTISITE_ENABLED', false)) {
                $this->setupMultisiteProject($project);
            } else {
                // Legacy mode: separate database per project
                $this->createProjectDatabase($project);
                $this->syncAllProjectTables($project);
            }
            
            $this->copyDefaultData($project);
            $this->createProjectAdmin($project);

            // Switch back to main database
            \DB::statement('USE '.config('database.connections.mysql.database'));

            $password = Project::generateProjectAdminPassword();
            $username = $project->code;
            $email = strtolower($project->code).'@project.local';

            // Create default permissions from settings
            $defaultPermissions = \App\Models\ProjectPermission::getDefaultPermissions();
            foreach ($defaultPermissions as $module => $permissions) {
                $project->permissions()->updateOrCreate(
                    ['module' => $module],
                    $permissions
                );
            }

            $apiToken = bin2hex(random_bytes(32));

            $project->update([
                'project_admin_username' => $project->project_admin_username,
                'project_admin_password' => $project->project_admin_password,
                'api_token' => $apiToken,
                'status' => 'active',
                'initialized_at' => now(),
            ]);

            $mode = env('MULTISITE_ENABLED', false) ? 'multisite' : 'legacy';
            return back()->with('alert', [
                'type' => 'success',
                'message' => "Khởi tạo website thành công! Chế độ: {$mode}. Database đã được kết nối và tài khoản quản trị đã được tạo.",
            ]);
        } catch (\Exception $e) {
            // Switch back to main database before updating project
            \DB::statement('USE '.config('database.connections.mysql.database'));
            $project->update(['status' => 'error']);

            return back()->with('alert', [
                'type' => 'error',
                'message' => 'Lỗi khởi tạo website: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Setup project in multisite mode (shared database)
     */
    private function setupMultisiteProject(Project $project)
    {
        \Log::info("Setting up project in multisite mode: {$project->code} (ID: {$project->id})");

        // Test connection to multisite database
        $multisiteDbName = env('MULTISITE_DB_DATABASE', 'multisite_db');
        $mainDb = config('database.connections.mysql.database');

        try {
            // Configure multisite database connection
            \Config::set('database.connections.multisite', [
                'driver' => 'mysql',
                'host' => env('MULTISITE_DB_HOST', env('DB_HOST', '127.0.0.1')),
                'port' => env('MULTISITE_DB_PORT', env('DB_PORT', '3306')),
                'database' => $multisiteDbName,
                'username' => env('MULTISITE_DB_USERNAME', env('DB_USERNAME', 'root')),
                'password' => env('MULTISITE_DB_PASSWORD', env('DB_PASSWORD', '')),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]);

            // Test connection
            \DB::connection('multisite')->getPdo();
            
            // Switch to multisite database
            \DB::setDefaultConnection('multisite');
            
            \Log::info("✅ Successfully connected to multisite database: {$multisiteDbName}");
            
            // Ensure tables exist in multisite database
            $this->ensureMultisiteTables();
            
        } catch (\Exception $e) {
            \Log::error("❌ Cannot connect to multisite database: {$multisiteDbName}. Error: " . $e->getMessage());
            
            // Switch back to main database
            \DB::setDefaultConnection('mysql');
            
            throw new \Exception("Multisite database '{$multisiteDbName}' không tồn tại hoặc không có quyền truy cập. Vui lòng kiểm tra cấu hình MULTISITE_DB_* trong .env");
        }
    }

    /**
     * Ensure all necessary tables exist in multisite database
     */
    private function ensureMultisiteTables()
    {
        $mainDb = config('database.connections.mysql.database');
        
        // Get list of tables from main database
        $allTables = \DB::connection('mysql')->select("SELECT table_name FROM information_schema.tables WHERE table_schema = '{$mainDb}' AND table_type = 'BASE TABLE'");

        $skipTables = ['migrations', 'password_reset_tokens', 'personal_access_tokens', 'tenants', 'projects', 'contracts', 'employees', 'project_settings', 'project_permissions', 'project_tickets', 'activity_logs'];

        foreach ($allTables as $tableObj) {
            $table = $tableObj->table_name;

            if (in_array($table, $skipTables)) {
                continue;
            }

            try {
                // Check if table exists in multisite database
                $exists = \DB::select("SHOW TABLES LIKE '{$table}'");
                
                if (empty($exists)) {
                    // Create table structure from main database
                    $result = \DB::connection('mysql')->select("SHOW CREATE TABLE `{$mainDb}`.`{$table}`");
                    if (!empty($result)) {
                        $sql = $result[0]->{'Create Table'};
                        
                        // Remove foreign key constraints for simplicity
                        $lines = explode("\n", $sql);
                        $filtered = [];
                        foreach ($lines as $line) {
                            if (stripos($line, 'CONSTRAINT') === false && stripos($line, 'FOREIGN KEY') === false) {
                                $filtered[] = $line;
                            }
                        }
                        $sql = implode("\n", $filtered);
                        $sql = preg_replace('/,\s*\)/', ')', $sql);
                        
                        \DB::statement($sql);
                        \Log::info("Created table {$table} in multisite database");
                    }
                }
            } catch (\Exception $e) {
                \Log::warning("Skip creating table {$table} in multisite database: ".$e->getMessage());
            }
        }
    }

    /**
     * Get standardized database name for project
     */
    private function getProjectDatabaseName(Project $project): string
    {
        $code = $project->code;

        // Fallback to project ID if code is empty
        if (empty($code)) {
            $code = 'project_'.$project->id;
        }

        // HOSTINGER FIX: Add user prefix for production
        if (app()->environment('production')) {
            // Extract user prefix from DB_USERNAME (e.g., u712054581_VGTApp -> u712054581)
            $username = env('DB_USERNAME', '');
            if (preg_match('/^(u\d+)_/', $username, $matches)) {
                $userPrefix = $matches[1];
                return $userPrefix . '_' . strtolower($code);
            }
        }

        return 'project_'.strtolower($code);
    }

    private function createProjectDatabase(Project $project)
    {
        $dbName = $this->getProjectDatabaseName($project);
        $mainDb = config('database.connections.mysql.database');

        \Log::info("Checking database connection: {$dbName} for project: {$project->code} (ID: {$project->id})");

        // MANUAL DATABASE SETUP: Don't create database automatically
        // Instead, just check if database exists and is accessible
        try {
            // Test connection to project database
            \DB::statement("USE `{$dbName}`");
            \Log::info("✅ Successfully connected to existing database: {$dbName}");
        } catch (\Exception $e) {
            \Log::error("❌ Cannot connect to database: {$dbName}. Error: " . $e->getMessage());
            
            // Switch back to main database
            \DB::statement("USE `{$mainDb}`");
            
            throw new \Exception("Database '{$dbName}' không tồn tại hoặc không có quyền truy cập. Vui lòng tạo database thủ công trong Hostinger hPanel và gán quyền cho user.");
        }
    }

    private function syncAllProjectTables(Project $project)
    {
        $dbName = $this->getProjectDatabaseName($project);

        $mainDb = config('database.connections.mysql.database');

        $allTables = \DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = '{$mainDb}' AND table_type = 'BASE TABLE'");

        \DB::statement("USE `{$dbName}`");
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $skipTables = ['migrations', 'password_reset_tokens', 'personal_access_tokens', 'tenants', 'projects', 'contracts', 'employees', 'project_settings', 'project_permissions', 'project_tickets', 'activity_logs'];

        foreach ($allTables as $tableObj) {
            $table = $tableObj->table_name;

            if (in_array($table, $skipTables)) {
                continue;
            }

            try {
                \DB::statement("DROP TABLE IF EXISTS `{$table}`");

                $result = \DB::select("SHOW CREATE TABLE `{$mainDb}`.`{$table}`");
                if (! empty($result)) {
                    $sql = $result[0]->{'Create Table'};
                    $lines = explode("\n", $sql);
                    $filtered = [];
                    foreach ($lines as $line) {
                        if (stripos($line, 'CONSTRAINT') === false && stripos($line, 'FOREIGN KEY') === false) {
                            $filtered[] = $line;
                        }
                    }
                    $sql = implode("\n", $filtered);
                    $sql = preg_replace('/,\s*\)/', ')', $sql);
                    \DB::statement($sql);
                }
            } catch (\Exception $e) {
                \Log::warning("Skip table {$table}: ".$e->getMessage());
            }
        }

        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
        \DB::statement("USE `{$mainDb}`");
    }

    private function createProjectAdmin(Project $project)
    {
        $mainDb = config('database.connections.mysql.database');
        
        $password = \App\Models\Project::generateProjectAdminPassword();
        $username = $project->code;
        $email = strtolower($project->code).'@project.local';

        if (env('MULTISITE_ENABLED', false)) {
            // Multisite mode: create user with project_id
            \DB::table('users')->updateOrInsert(
                [
                    'username' => $username,
                    'project_id' => $project->id
                ],
                [
                    'name' => 'CMS Admin - '.$project->code,
                    'email' => $email,
                    'password' => bcrypt($password),
                    'role' => 'cms',
                    'level' => 2,
                    'project_id' => $project->id,
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        } else {
            // Legacy mode: create user in project database
            $dbName = $this->getProjectDatabaseName($project);
            \DB::statement("USE `{$dbName}`");

            \DB::statement("
                INSERT INTO users (name, username, email, password, role, level, email_verified_at) 
                VALUES (?, ?, ?, ?, 'cms', 2, NOW())
                ON DUPLICATE KEY UPDATE password = VALUES(password)
            ", [
                'CMS Admin - '.$project->code,
                $username,
                $email,
                bcrypt($password),
            ]);

            \DB::statement("USE `{$mainDb}`");
        }

        $project->project_admin_username = $username;
        $project->project_admin_password = $password;
    }

    private function copyDefaultData(Project $project)
    {
        $mainDb = config('database.connections.mysql.database');
        $tablesToCopy = ['settings', 'menus', 'menu_items', 'widgets', 'widget_templates'];

        foreach ($tablesToCopy as $table) {
            try {
                // Get default data from main database
                $data = \DB::connection('mysql')->table($table)
                    ->where(function ($q) {
                        $q->whereNull('tenant_id')
                            ->orWhere('tenant_id', 0);
                    })
                    ->where(function ($q) {
                        $q->whereNull('project_id')
                            ->orWhere('project_id', 0);
                    })
                    ->get();

                if ($data->count() > 0) {
                    if (env('MULTISITE_ENABLED', false)) {
                        // Multisite mode: insert with project_id
                        foreach ($data as $row) {
                            $rowArray = (array) $row;
                            unset($rowArray['id']);
                            $rowArray['project_id'] = $project->id;
                            $rowArray['tenant_id'] = null;

                            // Check if record already exists for this project
                            $exists = \DB::table($table)
                                ->where('project_id', $project->id);
                            
                            // Add unique field check if available
                            if (isset($rowArray['key'])) {
                                $exists->where('key', $rowArray['key']);
                            } elseif (isset($rowArray['name'])) {
                                $exists->where('name', $rowArray['name']);
                            }
                            
                            if (!$exists->exists()) {
                                \DB::table($table)->insert($rowArray);
                            }
                        }
                    } else {
                        // Legacy mode: switch to project database
                        $dbName = $this->getProjectDatabaseName($project);
                        \DB::statement("USE `{$dbName}`");

                        foreach ($data as $row) {
                            $rowArray = (array) $row;
                            unset($rowArray['id']);
                            $rowArray['project_id'] = $project->id;
                            $rowArray['tenant_id'] = null;

                            \DB::table($table)->insert($rowArray);
                        }

                        \DB::statement("USE `{$mainDb}`");
                    }
                }
            } catch (\Exception $e) {
                \Log::warning("Skip copying data for {$table}: ".$e->getMessage());
            }
        }
    }

    public function config(Project $project)
    {
        $systemModules = collect(config('system_menu'))->map(function ($module) {
            return [
                'key' => $module['permission'],
                'title' => $module['title'],
                'description' => $module['description'],
            ];
        });

        $settings = ProjectSetting::where('project_id', $project->id)->pluck('value', 'key')->toArray();

        $remoteStats = null;
        if ($project->remote_url) {
            $remoteService = new \App\Services\RemoteProjectService;
            $result = $remoteService->getRemoteStats($project->remote_url, $project->code);
            if ($result['success']) {
                $remoteStats = $result['data']['stats'] ?? null;
            }
        }

        // Lấy danh sách user của project từ database project
        $users = collect();
        try {
            $dbName = $this->getProjectDatabaseName($project);

            $mainDb = config('database.connections.mysql.database');

            // Switch to project database
            \DB::statement("USE `{$dbName}`");

            // Get users from project database
            $users = collect(\DB::select("SELECT * FROM users WHERE role = 'cms'"));

            // Switch back to main database
            \DB::statement("USE `{$mainDb}`");

        } catch (\Exception $e) {
            \Log::error('Error getting project users: '.$e->getMessage());
            $users = collect();
        }

        return view('superadmin.projects.config', compact('project', 'systemModules', 'settings', 'users', 'remoteStats'));
    }

    public function updateConfig(Request $request, Project $project)
    {
        try {
            $allKeys = collect(config('system_menu'))->pluck('permission')->toArray();

            ProjectSetting::where('project_id', $project->id)
                ->whereIn('key', $allKeys)
                ->delete();

            if ($request->has('settings')) {
                foreach ($request->settings as $key => $value) {
                    ProjectSetting::set($project->id, $key, '1');
                }
            }

            if ($request->has('sync_data') && $request->sync_data) {
                if ($project->remote_url) {
                    $this->syncDataToRemote($project);
                } else {
                    $this->syncDataToProject($project);
                }
            }

            return back()->with('alert', [
                'type' => 'success',
                'message' => 'Cập nhật và đồng bộ dữ liệu thành công!',
            ]);
        } catch (\Exception $e) {
            return back()->with('alert', [
                'type' => 'error',
                'message' => 'Lỗi: '.$e->getMessage(),
            ]);
        }
    }

    private function syncDataToProject(Project $project)
    {
        $dbName = $this->getProjectDatabaseName($project);

        $mainDb = config('database.connections.mysql.database');

        $tablesToSync = ['settings', 'menus', 'menu_items', 'widgets', 'widget_templates', 'posts', 'product_categories', 'brands'];

        foreach ($tablesToSync as $table) {
            try {
                $data = \DB::table($table)
                    ->where(function ($q) {
                        $q->whereNull('tenant_id')->orWhere('tenant_id', 0);
                    })
                    ->where(function ($q) {
                        $q->whereNull('project_id')->orWhere('project_id', 0);
                    })
                    ->get();

                if ($data->count() > 0) {
                    \DB::statement("USE `{$dbName}`");
                    \DB::table($table)->truncate();

                    foreach ($data as $row) {
                        $rowArray = (array) $row;
                        $originalId = $rowArray['id'];
                        unset($rowArray['id']);
                        $rowArray['project_id'] = $project->id;
                        $rowArray['tenant_id'] = null;

                        \DB::table($table)->insert($rowArray);
                    }

                    \DB::statement("USE `{$mainDb}`");
                }
            } catch (\Exception $e) {
                \Log::warning("Skip syncing {$table}: ".$e->getMessage());
            }
        }
    }

    private function syncDataToRemote(Project $project)
    {
        $mainDb = config('database.connections.mysql.database');
        $tablesToSync = ['settings', 'menus', 'menu_items', 'widgets', 'posts', 'product_categories', 'brands'];

        $data = [];
        foreach ($tablesToSync as $table) {
            $rows = \DB::table($table)
                ->where(function ($q) {
                    $q->whereNull('tenant_id')->orWhere('tenant_id', 0);
                })
                ->where(function ($q) {
                    $q->whereNull('project_id')->orWhere('project_id', 0);
                })
                ->get()
                ->map(function ($row) use ($project) {
                    $rowArray = (array) $row;
                    unset($rowArray['id']);
                    $rowArray['project_id'] = $project->id;
                    $rowArray['tenant_id'] = null;

                    return $rowArray;
                })
                ->toArray();

            if (! empty($rows)) {
                $data[$table] = $rows;
            }
        }

        $remoteService = new \App\Services\RemoteProjectService;

        return $remoteService->syncRemoteData($project->remote_url, $project->code, $data);
    }

    public function exportConfig(Request $request, Project $project)
    {
        try {
            // Get current execution trace
            $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 10);
            $executionTrace = collect($trace)->map(function ($item) {
                return [
                    'file' => $item['file'] ?? 'unknown',
                    'line' => $item['line'] ?? 0,
                    'function' => $item['function'] ?? 'unknown',
                    'class' => $item['class'] ?? null,
                ];
            });

            // Get project settings
            $settings = ProjectSetting::where('project_id', $project->id)->get()->pluck('value', 'key');
            
            // Get system modules
            $systemModules = collect(config('system_menu'))->map(function ($module) use ($settings) {
                return [
                    'title' => $module['title'],
                    'description' => $module['description'],
                    'permission' => $module['permission'],
                    'enabled' => isset($settings[$module['permission']]) && $settings[$module['permission']] == '1',
                ];
            });

            // Get file change logs
            $logs = $this->getProjectLogs($project->code);

            // Get project users
            $users = $this->getProjectUsers($project);

            // Get remote stats if available
            $remoteStats = null;
            if ($project->remote_url) {
                try {
                    $remoteService = new \App\Services\RemoteProjectService;
                    $remoteStats = $remoteService->getRemoteStats($project->remote_url, $project->code);
                } catch (\Exception $e) {
                    $remoteStats = ['error' => $e->getMessage()];
                }
            }

            // Get current file being processed (if eval is used)
            $currentFile = $this->getCurrentProcessingFile();

            // Prepare export data
            $exportData = [
                'project' => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'code' => $project->code,
                    'status' => $project->status,
                    'remote_url' => $project->remote_url,
                    'created_at' => $project->created_at,
                    'updated_at' => $project->updated_at,
                ],
                'settings' => $settings,
                'modules' => $systemModules,
                'users' => $users,
                'remote_stats' => $remoteStats,
                'logs' => $logs->take(50), // Last 50 logs
                'debug_info' => [
                    'export_time' => now()->toISOString(),
                    'export_by' => auth()->user()?->name ?? 'System',
                    'execution_trace' => $executionTrace,
                    'current_file' => $currentFile,
                    'memory_usage' => memory_get_usage(true),
                    'peak_memory' => memory_get_peak_usage(true),
                    'included_files_count' => count(get_included_files()),
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                ],
                'file_analysis' => $this->analyzeProjectFiles($project),
            ];

            // Add eval detection if requested
            if ($request->get('include_eval')) {
                $exportData['eval_detection'] = $this->detectEvalUsage($project);
            }

            // Return as JSON or download
            if ($request->get('format') === 'download') {
                $filename = "project-{$project->code}-config-" . now()->format('Y-m-d-H-i-s') . '.json';
                
                return response()->json($exportData, 200, [
                    'Content-Type' => 'application/json',
                    'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                ]);
            }

            return response()->json($exportData, 200, [], JSON_PRETTY_PRINT);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Export failed',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    private function getProjectLogs(string $projectCode): \Illuminate\Support\Collection
    {
        $logPath = storage_path("logs/file-changes-{$projectCode}.log");
        
        if (!file_exists($logPath)) {
            return collect();
        }
        
        $content = file_get_contents($logPath);
        $lines = array_filter(explode("\n", $content));
        
        return collect($lines)->map(function($line) {
            $data = json_decode($line, true);
            return $data ? (object) $data : null;
        })->filter()->sortByDesc('timestamp');
    }

    private function getProjectUsers(Project $project): \Illuminate\Support\Collection
    {
        try {
            if ($project->remote_url) {
                // For remote projects, we might not have direct access
                return collect();
            }

            $dbName = $this->getProjectDatabaseName($project);
            $mainDb = config('database.connections.mysql.database');

            \DB::statement("USE `{$dbName}`");
            $users = \DB::table('users')->select('id', 'name', 'email', 'username', 'role', 'created_at')->get();
            \DB::statement("USE `{$mainDb}`");

            return collect($users);
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getCurrentProcessingFile(): array
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $currentFile = null;
        
        foreach ($trace as $item) {
            if (isset($item['file']) && !str_contains($item['file'], 'vendor/')) {
                $currentFile = [
                    'file' => $item['file'],
                    'line' => $item['line'] ?? 0,
                    'function' => $item['function'] ?? 'unknown',
                    'relative_path' => str_replace(base_path(), '', $item['file']),
                ];
                break;
            }
        }

        return $currentFile ?? ['file' => 'unknown', 'line' => 0, 'function' => 'unknown', 'relative_path' => 'unknown'];
    }

    private function analyzeProjectFiles(Project $project): array
    {
        $analysis = [
            'total_files' => 0,
            'recent_changes' => [],
            'file_types' => [],
            'large_files' => [],
        ];

        try {
            // Analyze recent file changes
            $directories = [
                'app/Http/Controllers',
                'app/Models',
                'resources/views',
                'routes',
                'config',
                'database/migrations'
            ];

            foreach ($directories as $dir) {
                $fullPath = base_path($dir);
                if (is_dir($fullPath)) {
                    $files = new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($fullPath)
                    );

                    foreach ($files as $file) {
                        if ($file->isFile()) {
                            $analysis['total_files']++;
                            
                            $extension = $file->getExtension();
                            $analysis['file_types'][$extension] = ($analysis['file_types'][$extension] ?? 0) + 1;
                            
                            // Check for recent changes (last 24 hours)
                            if (filemtime($file->getPathname()) > (time() - 86400)) {
                                $analysis['recent_changes'][] = [
                                    'file' => str_replace(base_path(), '', $file->getPathname()),
                                    'modified' => date('Y-m-d H:i:s', filemtime($file->getPathname())),
                                    'size' => $file->getSize(),
                                ];
                            }
                            
                            // Check for large files (> 1MB)
                            if ($file->getSize() > 1048576) {
                                $analysis['large_files'][] = [
                                    'file' => str_replace(base_path(), '', $file->getPathname()),
                                    'size' => $file->getSize(),
                                    'size_mb' => round($file->getSize() / 1048576, 2),
                                ];
                            }
                        }
                    }
                }
            }

            // Sort by modification time
            usort($analysis['recent_changes'], function($a, $b) {
                return strtotime($b['modified']) - strtotime($a['modified']);
            });

            // Limit results
            $analysis['recent_changes'] = array_slice($analysis['recent_changes'], 0, 20);
            $analysis['large_files'] = array_slice($analysis['large_files'], 0, 10);

        } catch (\Exception $e) {
            $analysis['error'] = $e->getMessage();
        }

        return $analysis;
    }

    private function detectEvalUsage(Project $project): array
    {
        $evalDetection = [
            'found_eval' => false,
            'eval_files' => [],
            'suspicious_functions' => [],
        ];

        try {
            $directories = [
                'app',
                'resources/views',
                'routes',
                'config'
            ];

            $suspiciousFunctions = ['eval', 'exec', 'system', 'shell_exec', 'passthru', 'file_get_contents', 'file_put_contents'];

            foreach ($directories as $dir) {
                $fullPath = base_path($dir);
                if (is_dir($fullPath)) {
                    $files = new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($fullPath)
                    );

                    foreach ($files as $file) {
                        if ($file->isFile() && in_array($file->getExtension(), ['php', 'blade.php'])) {
                            $content = file_get_contents($file->getPathname());
                            
                            foreach ($suspiciousFunctions as $func) {
                                if (strpos($content, $func . '(') !== false) {
                                    $evalDetection['suspicious_functions'][] = [
                                        'file' => str_replace(base_path(), '', $file->getPathname()),
                                        'function' => $func,
                                        'lines' => $this->findFunctionLines($content, $func),
                                    ];
                                    
                                    if ($func === 'eval') {
                                        $evalDetection['found_eval'] = true;
                                        $evalDetection['eval_files'][] = str_replace(base_path(), '', $file->getPathname());
                                    }
                                }
                            }
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            $evalDetection['error'] = $e->getMessage();
        }

        return $evalDetection;
    }

    private function findFunctionLines(string $content, string $function): array
    {
        $lines = explode("\n", $content);
        $foundLines = [];

        foreach ($lines as $lineNumber => $line) {
            if (strpos($line, $function . '(') !== false) {
                $foundLines[] = [
                    'line_number' => $lineNumber + 1,
                    'content' => trim($line),
                ];
            }
        }

        return array_slice($foundLines, 0, 5); // Limit to 5 occurrences per file
    }

    public function exportViewer(Request $request, Project $project)
    {
        // Get export data
        $exportRequest = $request->duplicate();
        $exportRequest->query->set('include_eval', '1'); // Always include eval detection for viewer
        
        $response = $this->exportConfig($exportRequest, $project);
        $exportData = $response->getData(true);

        return view('superadmin.projects.export-viewer', compact('project', 'exportData'));
    }
}
