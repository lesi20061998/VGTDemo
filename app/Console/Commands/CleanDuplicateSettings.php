<?php

namespace App\Console\Commands;

use App\Models\ProjectSettingModel;
use Illuminate\Console\Command;

class CleanDuplicateSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:clean-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean duplicate settings keys in project database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning duplicate settings keys...');

        try {
            $cleaned = ProjectSettingModel::cleanDuplicateKeys();
            $this->info("Successfully cleaned {$cleaned} duplicate keys.");
        } catch (\Exception $e) {
            $this->error('Error cleaning duplicate keys: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
