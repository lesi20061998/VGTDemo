<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;

class CheckExportRoutes extends Command
{
  protected $signature = 'project:check-routes {zipFile}';

  protected $description = 'Check routes in exported CMS zip file';

  public function handle()
  {
    $zipFile = $this->argument('zipFile');
    $fullPath = storage_path("app/{$zipFile}");

    if (! file_exists($fullPath)) {
      $this->error("Zip file not found: {$fullPath}");

      return 1;
    }

    $zip = new ZipArchive;
    if ($zip->open($fullPath) === true) {

      // Check for routes files
      $routeFiles = ['routes/web.php', 'routes/api.php', 'routes/console.php'];

      foreach ($routeFiles as $routeFile) {
        $found = false;
        for ($i = 0; $i < $zip->numFiles; $i++) {
          $filename = $zip->getNameIndex($i);
          if ($filename === $routeFile) {
            $found = true;
            $this->info(" Found: {$routeFile}");

            // Extract and show first few lines
            $content = $zip->getFromName($routeFile);
            $lines = explode("\n", $content);
            $this->info(" First 10 lines of {$routeFile}:");
            for ($j = 0; $j < min(10, count($lines)); $j++) {
              $this->info(' '.($j + 1).': '.$lines[$j]);
            }
            $this->info('');
            break;
          }
        }

        if (! $found) {
          $this->error(" Missing: {$routeFile}");
        }
      }

      // Check for middleware
      $middlewareFile = 'app/Http/Middleware/StandaloneCMS.php';
      $found = false;
      for ($i = 0; $i < $zip->numFiles; $i++) {
        $filename = $zip->getNameIndex($i);
        if ($filename === $middlewareFile) {
          $found = true;
          $this->info(" Found: {$middlewareFile}");
          break;
        }
      }

      if (! $found) {
        $this->error(" Missing: {$middlewareFile}");
      }

      // Check bootstrap/app.php
      $bootstrapFile = 'bootstrap/app.php';
      $found = false;
      for ($i = 0; $i < $zip->numFiles; $i++) {
        $filename = $zip->getNameIndex($i);
        if ($filename === $bootstrapFile) {
          $found = true;
          $this->info(" Found: {$bootstrapFile}");

          // Show content
          $content = $zip->getFromName($bootstrapFile);
          $lines = explode("\n", $content);
          $this->info(' bootstrap/app.php content:');
          for ($j = 0; $j < min(15, count($lines)); $j++) {
            $this->info(' '.($j + 1).': '.$lines[$j]);
          }
          break;
        }
      }

      if (! $found) {
        $this->error(" Missing: {$bootstrapFile}");
      }

      $zip->close();

    } else {
      $this->error('Cannot open zip file');

      return 1;
    }

    return 0;
  }
}
