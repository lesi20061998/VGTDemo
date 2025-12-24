<?php

namespace App\Console\Commands;

use App\Services\ProjectPasswordService;
use Illuminate\Console\Command;

class MigrateProjectPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:migrate-passwords {--dry-run : Show what would be migrated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing project passwords to support plain text display';

    /**
     * Execute the console command.
     */
    public function handle(ProjectPasswordService $passwordService): int
    {
        $this->info('Starting project password migration...');
        
        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }
        
        try {
            if ($this->option('dry-run')) {
                // Count projects that would be migrated
                $count = \App\Models\Project::whereNotNull('project_admin_password')
                    ->whereNull('project_admin_password_plain')
                    ->count();
                
                $this->info("Would migrate {$count} projects");
                return Command::SUCCESS;
            }
            
            $migrated = $passwordService->migrateExistingPasswords();
            
            $this->info("Successfully migrated {$migrated} project passwords");
            
            if ($migrated > 0) {
                $this->warn('New passwords have been generated for migrated projects.');
                $this->warn('Please update your documentation with the new passwords.');
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Migration failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
