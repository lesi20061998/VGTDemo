<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\FeaturePack;
use App\Models\Project;
use App\Models\ProjectPermission;
use App\Models\ProjectSetting;
use App\Models\User;
use App\Services\RemoteProjectService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class ProjectController extends Controller implements HasMiddleware
{
  public static function middleware(): array
  {
    return [
      new Middleware('can:manage-projects', except: ['index', 'show']),
    ];
  }

  public function index()
  {
    $query = Project::with(['admin', 'createdBy'])->latest();

    // If user is dev, only show projects they are assigned to
    if (auth()->check() && (auth()->user()->role === 'dev' || auth()->user()->hasRole('dev'))) {
      $query->where(function ($q) {
        $q->whereJsonContains('employee_ids', (string) auth()->id())
          ->orWhereJsonContains('employee_ids', auth()->id());
      });
    }

    $projects = $query->get();

    $infectedProjects = [];
    foreach ($projects as $project) {
      $logPath = storage_path('logs/file-changes-'.$project->code.'.log');
      if (File::exists($logPath)) {
        $content = File::get($logPath);
        if (str_contains($content, 'Độc Hại') || str_contains($content, '\u0110\u1ed9c H\u1ea1i')) {
          $infectedProjects[] = $project->id;
        }
      }
    }

    return view('superadmin.projects.index', compact('projects', 'infectedProjects'));
  }

  public function create()
  {
    $contracts = Contract::whereIn('status', ['active', 'completed'])->get();
    $employees = User::whereIn('role', ['super_admin', 'account'])
      ->orWhereHas('roles', function ($q) {
        $q->whereIn('name', ['super_admin', 'account']);
      })->get();
    $devs = User::where('role', 'dev')
      ->orWhereHas('roles', function ($q) {
        $q->where('name', 'dev');
      })->get();
    $featurePacks = FeaturePack::where('is_active', true)->orderBy('group_name')->orderBy('name')->get();

    return view('superadmin.projects.create', compact('contracts', 'employees', 'devs', 'featurePacks'));
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'contract_id' => 'required|exists:contracts,id',
      'employee_id' => 'required|exists:users,id',
      'name' => 'required|string|max:255',
      'code' => 'required|string|unique:projects,code',
      'status' => 'nullable|string',
      'technical_requirements' => 'nullable|string',
      'features' => 'nullable|string',
      'cms_features' => 'nullable|array',
      'environment' => 'nullable|string',
      'notes' => 'nullable|string',
      'dev_ids' => 'nullable|array',
      'dev_ids.*' => 'exists:users,id',
    ]);

    $contract = Contract::findOrFail($request->contract_id);
    $employee = User::findOrFail($request->employee_id);

    $baseUrl = config('app.url');
    $subdomain = rtrim($baseUrl, '/').'/'.ltrim($request->code, '/');

    $project = Project::create([
      'contract_id' => $request->contract_id,
      'name' => $request->name,
      'code' => $request->code,
      'subdomain' => $subdomain,
      'client_name' => $contract->client_name ?? 'TBD',
      'contract_value' => $contract->contract_value,
      'start_date' => $contract->start_date ?? now(),
      'deadline' => $contract->end_date ?? now()->addMonth(),
      'technical_requirements' => $contract->technical_requirements,
      'features' => $contract->features,
      'cms_features' => $request->cms_features ?? [],
      'environment' => $request->environment,
      'notes' => $request->notes,
      'admin_id' => $request->employee_id,
      'employee_ids' => array_values(array_unique(array_merge([$request->employee_id], $request->dev_ids ?? []))),
      'created_by' => auth()->id() ?? $contract->employee_id,
      'status' => $request->status ?? 'pending',
    ]);

    return redirect()->route('superadmin.projects.show', $project)->with('alert', [
      'type' => 'success',
      'message' => 'Tạo và phân phối dự án thành công!',
    ]);
  }

  public function show(Project $project)
  {
    if (auth()->check() && (auth()->user()->role === 'dev' || auth()->user()->hasRole('dev'))) {
      $employeeIds = is_array($project->employee_ids) ? $project->employee_ids : json_decode($project->employee_ids, true) ?? [];
      if (! in_array(auth()->id(), $employeeIds)) {
        abort(403, 'Bạn không có quyền xem dự án này.');
      }
    }

    $project->load(['admin', 'createdBy']);

    $featurePacks = FeaturePack::where('is_active', true)->orderBy('group_name')->orderBy('name')->get();

    return view('superadmin.projects.show', compact('project', 'featurePacks'));
  }

  public function edit(Project $project)
  {
    $contracts = collect();
    $employees = User::whereIn('role', ['super_admin', 'account'])
      ->orWhereHas('roles', function ($q) {
        $q->whereIn('name', ['super_admin', 'account']);
      })->get();
    $devs = User::where('role', 'dev')
      ->orWhereHas('roles', function ($q) {
        $q->where('name', 'dev');
      })->get();
    $featurePacks = FeaturePack::where('is_active', true)->orderBy('group_name')->orderBy('name')->get();

    return view('superadmin.projects.edit', compact('project', 'contracts', 'employees', 'devs', 'featurePacks'));
  }

  public function update(Request $request, Project $project)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'subdomain' => 'required|string|max:255',
      'employee_id' => 'required|exists:users,id',
      'dev_ids' => 'nullable|array',
      'dev_ids.*' => 'exists:users,id',
      'notes' => 'nullable|string',
      'start_date' => 'nullable|date',
      'deadline' => 'nullable|date|after_or_equal:start_date',
      'status' => 'nullable|in:pending,active,assigned,in_progress,on_hold,error,completed',
      'contract_value' => 'nullable|numeric',
      'technical_requirements' => 'nullable|string',
      'features' => 'nullable|string',
      'cms_features' => 'nullable|array',
      'environment' => 'nullable|string',
    ]);

    $project->update([
      'name' => $request->name,
      'subdomain' => $request->subdomain,
      'admin_id' => $request->employee_id,
      'employee_ids' => array_values(array_unique(array_merge([$request->employee_id], $request->dev_ids ?? []))),
      'notes' => $request->notes,
      'start_date' => $request->start_date,
      'deadline' => $request->deadline,
      'status' => $request->status,
      'contract_value' => $request->contract_value,
      'technical_requirements' => $request->technical_requirements,
      'features' => $request->features,
      'cms_features' => $request->cms_features ?? [],
      'environment' => $request->environment,
    ]);

    return redirect()->route('superadmin.projects.show', $project)->with('alert', [
      'type' => 'success',
      'message' => 'Cập nhật dự án thành công!',
    ]);
  }

  public function updateProgress(Request $request, Project $project)
  {
    $validated = $request->validate([
      'environment' => 'nullable|string',
      'notes' => 'nullable|string',
      'status' => 'required|in:pending,active,assigned,in_progress,on_hold,error,completed',
    ]);

    $project->update($validated);

    return back()->with('alert', [
      'type' => 'success',
      'message' => 'Đã gửi báo cáo kết quả/tiến độ dự án cho PM thành công!',
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

  public function createWebsite(Request $request, Project $project)
  {
    // Update feature packs if provided
    if ($request->has('cms_features')) {
      $project->update(['cms_features' => $request->cms_features]);
    }

    // Basic permission check
    if (! auth()->check()) {
      return back()->with('alert', [
        'type' => 'error',
        'message' => 'Bạn cần đăng nhập để thực hiện chức năng này!',
      ]);
    }

    // DEMO MODE: Temporarily disable all permission checks
    /*
    // Check if user has permission (admin/superadmin with level 0-1)
    $user = auth()->user();
    $hasPermission = (
      in_array($user->role, ['admin', 'superadmin']) &&
      ($user->level ?? 99) <= 1
    );

    if (!$hasPermission) {
      return back()->with('alert', [
        'type' => 'error',
        'message' => 'Bạn không có quyền tạo website! Cần role admin/superadmin với level <= 1. Hiện tại: role=' . ($user->role ?? 'null') . ', level=' . ($user->level ?? 'null'),
      ]);
    }
    */

    // DEMO MODE: Allow creating website for assigned or active projects
    if (! in_array($project->status, ['assigned', 'active'])) {
      return back()->with('alert', [
        'type' => 'error',
        'message' => 'Chỉ có thể tạo website cho dự án đã được phân phối (assigned) hoặc đang hoạt động (active)!',
      ]);
    }

    try {
      // DEMO MODE: Create project admin user in shared database
      \Log::info("Demo Mode: Creating website for project {$project->code} with shared database");

      // Generate admin credentials
      $password = Project::generateProjectAdminPassword();
      $username = $project->code;
      $email = strtolower($project->code).'@project.local';

      // Create CMS admin user in shared database (without tenant_id)
      \DB::table('users')->updateOrInsert(
        [
          'username' => $username,
        ],
        [
          'name' => 'CMS Admin - '.$project->code,
          'email' => $email,
          'password' => bcrypt($password),
          'role' => 'cms',
          'level' => 2,
          'tenant_id' => null, // No tenant in demo mode
          'project_ids' => json_encode([$project->id]), // Use project_ids for project scoping
          'email_verified_at' => now(),
          'created_at' => now(),
          'updated_at' => now(),
        ]
      );

      // Create default permissions from settings (without database switching)
      try {
        $defaultPermissions = ProjectPermission::getDefaultPermissions();
        foreach ($defaultPermissions as $module => $permissions) {
          $project->permissions()->updateOrCreate(
            ['module' => $module],
            $permissions
          );
        }
      } catch (\Exception $e) {
        \Log::warning('Could not create permissions: '.$e->getMessage());
      }

      // KHỞI TẠO DỮ LIỆU RIÊNG CHO PROJECT (NON-SYNCHRONIZED DATA)
      $this->seedProjectTheme($project);
      $this->seedProjectMenus($project);

      $apiToken = bin2hex(random_bytes(32));

      // Update project with generated credentials
      $project->update([
        'project_admin_username' => $username,
        'project_admin_password' => bcrypt($password),
        'project_admin_password_plain' => encrypt($password),
        'api_token' => $apiToken,
        'status' => 'active',
        'initialized_at' => now(),
      ]);

      \Log::info(" Created CMS user: {$username} with role=cms, level=2, project_ids=[{$project->id}]");

      return back()->with('alert', [
        'type' => 'success',
        'message' => " Demo Mode: Website '{$project->name}' đã được tạo thành công!\n\nUsername: {$username}\nPassword: {$password}\nRole: cms\nLevel: 2\nProject IDs: [{$project->id}]\n\n(Sử dụng shared database với project_ids scoping)",
      ]);

    } catch (\Exception $e) {
      $project->update(['status' => 'error']);

      return back()->with('alert', [
        'type' => 'error',
        'message' => 'Lỗi tạo website: '.$e->getMessage(),
      ]);
    }
  }

  // DISABLED FOR DEMO: Database setup methods
  /*
  private function setupSharedProject(Project $project)
  {
    \Log::info("Setting up project in shared database mode: {$project->code} (ID: {$project->id})");
    \Log::info(" Project setup complete - using shared database with project_id: {$project->id}");
  }

  private function copyDefaultData(Project $project)
  {
    $tablesToCopy = ['settings', 'menus', 'menu_items', 'widgets', 'widget_templates'];
    // ... copy data logic
  }

  private function createProjectAdmin(Project $project)
  {
    // ... create admin user logic
  }
  */

  /**
   * Setup project using shared database (no separate database creation)
   */
  private function setupSharedProject(Project $project)
  {
    \Log::info("S hared database mode: {$project->code} (ID: {$project->id})");

    // No database creation needed - just use main database
    // All data will be scoped by project_id

    \Log::info(" Project setup complete - using shared database with project_id: {$project->id}");
  }

  // COMMENTED OUT: Multisite database creation
  // This was trying to create separate databases which causes issues on shared hosting
  /*
  private function setupMultisiteProject(Project $project)
  {
    \Log::info("Setting up project in multisite mode: {$project->code} (ID: {$project->id})");

    // Use fixed multisite database configuration
    $multisiteDbName = env('MULTISITE_DB_DATABASE', 'u712054581_Database_01');
    $mainDb = config('database.connections.mysql.database');

    try {
      // Configure multisite database connection
      \Config::set('database.connections.multisite', [
        'driver' => 'mysql',
        'host' => env('MULTISITE_DB_HOST', '127.0.0.1'),
        'port' => env('MULTISITE_DB_PORT', '3306'),
        'database' => $multisiteDbName,
        'username' => env('MULTISITE_DB_USERNAME', 'u712054581_Database_01'),
        'password' => env('MULTISITE_DB_PASSWORD', ''),
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

      \Log::info(" Successfully connected to multisite database: {$multisiteDbName}");

      // Ensure tables exist in multisite database
      $this->ensureMultisiteTables();

    } catch (\Exception $e) {
      \Log::error(" Cannot connect to multisite database: {$multisiteDbName}. Error: " . $e->getMessage());

      // Switch back to main database
      \DB::setDefaultConnection('mysql');

      throw new \Exception("Multisite database '{$multisiteDbName}' không tồn tại hoặc không có quyền truy cập. Vui lòng kiểm tra cấu hình MULTISITE_DB_* trong .env");
    }
  }
  */

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
          if (! empty($result)) {
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

        return $userPrefix.'_'.strtolower($code);
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
      \Log::info(" Successfully connected to existing database: {$dbName}");
    } catch (\Exception $e) {
      \Log::error(" Cannot connect to database: {$dbName}. Error: ".$e->getMessage());

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
    $password = Project::generateProjectAdminPassword();
    $username = $project->code;
    $email = strtolower($project->code).'@project.local';

    // SHARED DATABASE MODE: Create user with project_id in same database
    \DB::table('users')->updateOrInsert(
      [
        'username' => $username,
        'project_id' => $project->id,
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

    $project->project_admin_username = $username;
    $project->project_admin_password = $password;
  }

  private function seedProjectTheme(Project $project)
  {
    \Log::info("Seeding project theme and basic settings for project {$project->id}");
    // Chỉ copy bảng settings (chứa các cấu hình cơ bản, theme, font, colors...)
    // KHÔNG copy widgets, widget_templates vì chúng là dữ liệu dùng chung (Synchronized)
    $tablesToCopy = ['settings'];

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
          foreach ($data as $row) {
            $rowArray = (array) $row;
            unset($rowArray['id']);
            $rowArray['project_id'] = $project->id;
            $rowArray['tenant_id'] = null;

            $exists = \DB::table($table)->where('project_id', $project->id);

            if (isset($rowArray['key'])) {
              $exists->where('key', $rowArray['key']);
            } elseif (isset($rowArray['name'])) {
              $exists->where('name', $rowArray['name']);
            }

            if (! $exists->exists()) {
              \DB::table($table)->insert($rowArray);
            }
          }
        }
      } catch (\Exception $e) {
        \Log::warning("Skip seeding data for {$table}: ".$e->getMessage());
      }
    }
  }

  private function seedProjectMenus(Project $project)
  {
    \Log::info("Seeding empty basic menus for project {$project->id}");
    // Chỉ tạo khung Menu rỗng để khách hàng tự thêm Menu Items
    // Các menu cơ bản thường có: Main Menu, Footer Menu
    $basicMenus = [
      ['name' => 'Main Menu', 'slug' => 'main-menu', 'location' => 'header', 'is_active' => true],
      ['name' => 'Footer Menu', 'slug' => 'footer-menu', 'location' => 'footer', 'is_active' => true],
    ];

    foreach ($basicMenus as $menu) {
      try {
        $exists = \DB::table('menus')
          ->where('project_id', $project->id)
          ->where('slug', $menu['slug'])
          ->exists();

        if (! $exists) {
          \DB::table('menus')->insert(array_merge($menu, [
            'project_id' => $project->id,
            'tenant_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
          ]));
        }
      } catch (\Exception $e) {
        \Log::warning("Could not seed menu {$menu['slug']}: ".$e->getMessage());
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
      $remoteService = new RemoteProjectService;
      $result = $remoteService->getRemoteStats($project->remote_url, $project->code);
      if ($result['success']) {
        $remoteStats = $result['data']['stats'] ?? null;
      }
    }

    // DEMO MODE: Lấy danh sách CMS users từ shared database theo project_ids
    $users = User::where('role', 'cms')
      ->get()
      ->filter(function ($u) use ($project) {
        $ids = is_array($u->project_ids) ? $u->project_ids : json_decode($u->project_ids ?? '[]', true);

        return in_array($project->id, $ids ?? []);
      })
      ->values();

    $featurePacks = FeaturePack::where('is_active', true)->orderBy('group_name')->orderBy('name')->get();

    return view('superadmin.projects.config', compact('project', 'systemModules', 'settings', 'users', 'remoteStats', 'featurePacks'));
  }

  public function resetAdminAccount(Request $request, Project $project)
  {
    $request->validate([
      'username' => 'required|string|max:100',
      'email' => 'required|email|max:255',
      'password' => 'required|string|min:6',
    ]);

    $password = $request->password;
    $username = $request->username;
    $email = $request->email;

    // Tìm user CMS hiện tại của project trong shared database
    $user = User::where('role', 'cms')
      ->get()
      ->first(function ($u) use ($project) {
        $ids = is_array($u->project_ids) ? $u->project_ids : json_decode($u->project_ids ?? '[]', true);

        return in_array($project->id, $ids ?? []);
      });

    if ($user) {
      // UPDATE user hiện tại
      $user->update([
        'name' => 'Admin '.$project->name,
        'username' => $username,
        'email' => $email,
        'password' => $password,
      ]);
    } else {
      // Chưa có user → CREATE mới
      User::create([
        'name' => 'Admin '.$project->name,
        'username' => $username,
        'email' => $email,
        'password' => $password,
        'role' => 'cms',
        'level' => 2,
        'project_ids' => [$project->id],
        'email_verified_at' => now(),
      ]);
    }

    // Cập nhật thông tin credentials trên project
    $project->update([
      'project_admin_username' => $username,
      'project_admin_password' => bcrypt($password),
      'project_admin_password_plain' => encrypt($password),
      'password_updated_at' => now(),
      'password_updated_by' => auth()->id(),
    ]);

    return back()->with('alert', [
      'type' => 'success',
      'message' => 'Đã cập nhật tài khoản CMS thành công! Username: '.$username.' | Password: '.$password,
    ]);
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

      if ($request->has('cms_features')) {
        $project->update(['cms_features' => $request->cms_features]);
      } else {
        $project->update(['cms_features' => []]);
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

    $remoteService = new RemoteProjectService;

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
          $remoteService = new RemoteProjectService;
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
        $filename = "project-{$project->code}-config-".now()->format('Y-m-d-H-i-s').'.json';

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

  private function getProjectLogs(string $projectCode): Collection
  {
    $logPath = storage_path("logs/file-changes-{$projectCode}.log");

    if (! file_exists($logPath)) {
      return collect();
    }

    $content = file_get_contents($logPath);
    $lines = array_filter(explode("\n", $content));

    return collect($lines)->map(function ($line) {
      $data = json_decode($line, true);

      return $data ? (object) $data : null;
    })->filter()->sortByDesc('timestamp');
  }

  private function getProjectUsers(Project $project): Collection
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
      if (isset($item['file']) && ! str_contains($item['file'], 'vendor/')) {
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
        'database/migrations',
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
      usort($analysis['recent_changes'], function ($a, $b) {
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
        'config',
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
                if (strpos($content, $func.'(') !== false) {
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
      if (strpos($line, $function.'(') !== false) {
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
