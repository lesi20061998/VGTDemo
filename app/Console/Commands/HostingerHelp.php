<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class HostingerHelp extends Command
{
    protected $signature = 'hostinger:help';
    protected $description = 'Show all available Hostinger management commands';

    public function handle()
    {
        $this->info("🎯 HOSTINGER MANAGEMENT TOOLS");
        $this->info("=============================");
        $this->info("");
        
        $this->info("📋 AVAILABLE COMMANDS:");
        $this->info("");
        
        $commands = [
            [
                'command' => 'hostinger:database',
                'description' => 'Comprehensive database management',
                'examples' => [
                    'hostinger:database list',
                    'hostinger:database instructions hd001',
                    'hostinger:database test hd001',
                    'hostinger:database test --all',
                    'hostinger:database status hd001'
                ]
            ],
            [
                'command' => 'hostinger:setup',
                'description' => 'Setup and configure for Hostinger',
                'examples' => [
                    'hostinger:setup check',
                    'hostinger:setup configure',
                    'hostinger:setup env',
                    'hostinger:setup permissions'
                ]
            ],
            [
                'command' => 'hostinger:checklist',
                'description' => 'Deployment readiness checklist',
                'examples' => [
                    'hostinger:checklist',
                    'hostinger:checklist hd001',
                    'hostinger:checklist --all'
                ]
            ],
            [
                'command' => 'project:export-standalone',
                'description' => 'Export standalone CMS projects',
                'examples' => [
                    'project:export-standalone hd001'
                ]
            ]
        ];
        
        foreach ($commands as $cmd) {
            $this->info("🔧 {$cmd['command']}");
            $this->info("   {$cmd['description']}");
            $this->info("");
            $this->info("   Examples:");
            foreach ($cmd['examples'] as $example) {
                $this->info("   • php artisan {$example}");
            }
            $this->info("");
        }
        
        $this->info("📝 TYPICAL WORKFLOW:");
        $this->info("===================");
        $this->info("");
        $this->info("1️⃣ Check environment:");
        $this->info("   php artisan hostinger:setup check");
        $this->info("");
        $this->info("2️⃣ List all databases:");
        $this->info("   php artisan hostinger:database list");
        $this->info("");
        $this->info("3️⃣ Get creation instructions:");
        $this->info("   php artisan hostinger:database instructions {project_code}");
        $this->info("");
        $this->info("4️⃣ Test database connection:");
        $this->info("   php artisan hostinger:database test {project_code}");
        $this->info("");
        $this->info("5️⃣ Check deployment readiness:");
        $this->info("   php artisan hostinger:checklist {project_code}");
        $this->info("");
        $this->info("6️⃣ Export project:");
        $this->info("   php artisan project:export-standalone {project_code}");
        $this->info("");
        
        $this->info("🚨 TROUBLESHOOTING:");
        $this->info("===================");
        $this->info("");
        $this->info("❌ Database connection failed:");
        $this->info("   • Check database exists in Hostinger hPanel");
        $this->info("   • Verify user has ALL PRIVILEGES");
        $this->info("   • Check .env DB_USERNAME and DB_PASSWORD");
        $this->info("");
        $this->info("❌ Project creation failed:");
        $this->info("   • Run: php artisan hostinger:database test {project_code}");
        $this->info("   • Check database permissions");
        $this->info("   • Verify project status is 'assigned'");
        $this->info("");
        $this->info("❌ Export issues:");
        $this->info("   • Run: php artisan hostinger:checklist {project_code}");
        $this->info("   • Fix all checklist items");
        $this->info("   • Ensure database has admin user");
        $this->info("");
        
        $this->info("📞 SUPPORT:");
        $this->info("===========");
        $this->info("");
        $this->info("For additional help with specific commands:");
        $this->info("• php artisan hostinger:database help");
        $this->info("• php artisan hostinger:setup help");
        $this->info("");
        
        return 0;
    }
}