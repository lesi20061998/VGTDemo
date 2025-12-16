<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Widgets\WidgetRegistry;

class RegisterWidgetTemplates extends Command
{
    protected $signature = 'widgets:register';
    protected $description = 'Register all widget templates';

    public function handle()
    {
        $widgets = WidgetRegistry::all();
        $categories = WidgetRegistry::getByCategory();
        
        $this->info('Đăng ký Widget Templates:');
        $this->line('');
        
        foreach ($categories as $category => $categoryWidgets) {
            $this->info("📁 {$category} ({" . count($categoryWidgets) . "} widgets)");
            foreach ($categoryWidgets as $widget) {
                $this->line("  ✓ {$widget['name']} ({$widget['type']})");
            }
            $this->line('');
        }
        
        $this->info('Tổng cộng: ' . count($widgets) . ' widget templates đã được đăng ký');
        return 0;
    }
}