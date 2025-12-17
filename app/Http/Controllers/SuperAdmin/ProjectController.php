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
            $this->createProjectDatabase($project);
            $this->syncAllProjectTables($project);
            $this->copyDefaultData($project);
            $this->createProjectAdmin($project);

            // Switch back to main database
            \DB::statement('USE '.config('database.connections.mysql.database'));

            $password = Project::generateProjectAdminPassword();
            $username = $project->code;
            $email = strtolower($project->code).'@project.local';

            // User admin is created in project database during createProjectDatabase()

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

            return back()->with('alert', [
                'type' => 'success',
                'message' => 'Tạo website và database thành công! Tài khoản quản trị đã được tạo.',
            ]);
        } catch (\Exception $e) {
            // Switch back to main database before updating project
            \DB::statement('USE '.config('database.connections.mysql.database'));
            $project->update(['status' => 'error']);

            return back()->with('alert', [
                'type' => 'error',
                'message' => 'Lỗi tạo website: '.$e->getMessage(),
            ]);
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

        return 'project_'.strtolower($code);
    }

    private function createProjectDatabase(Project $project)
    {
        $dbName = $this->getProjectDatabaseName($project);

        $mainDb = config('database.connections.mysql.database');

        \Log::info("Creating database: {$dbName} for project: {$project->code} (ID: {$project->id})");

        \DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        \DB::statement("USE `{$dbName}`");
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
        $dbName = $this->getProjectDatabaseName($project);

        $mainDb = config('database.connections.mysql.database');

        \DB::statement("USE `{$dbName}`");

        $password = \App\Models\Project::generateProjectAdminPassword();
        $username = $project->code;
        $email = strtolower($project->code).'@project.local';

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

        $project->project_admin_username = $username;
        $project->project_admin_password = $password;
    }

    private function copyDefaultData(Project $project)
    {
        $dbName = $this->getProjectDatabaseName($project);

        $mainDb = config('database.connections.mysql.database');

        $tablesToCopy = ['settings', 'menus', 'menu_items', 'widgets', 'widget_templates'];

        foreach ($tablesToCopy as $table) {
            try {
                $data = \DB::table($table)
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
}
