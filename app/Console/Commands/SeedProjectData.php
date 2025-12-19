<?php

namespace App\Console\Commands;

use Database\Seeders\ProjectSampleDataSeeder;
use Illuminate\Console\Command;

class SeedProjectData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:seed {projectCode=hd001 : The project code to seed data for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed sample data for a specific project database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $projectCode = $this->argument('projectCode');

        $this->info("Starting to seed sample data for project: {$projectCode}");

        try {
            $seeder = new ProjectSampleDataSeeder;
            $seeder->setCommand($this);
            $seeder->run($projectCode);

            $this->info("✅ Successfully seeded sample data for project: {$projectCode}");
            $this->info("You can now view the data at: /{$projectCode}/admin/products");

        } catch (\Exception $e) {
            $this->error('❌ Error seeding data: '.$e->getMessage());
            $this->error('File: '.$e->getFile().':'.$e->getLine());

            return 1;
        }

        return 0;
    }
}
