<?php

namespace Database\Seeders;

use App\Models\ProjectBrand;
use App\Models\ProjectProduct;
use App\Models\ProjectProductCategory;
use Illuminate\Database\Seeder;

class ProjectDatabaseSeeder extends Seeder
{
  /**
   * Seed the project database with test data.
   * Each project will have different data based on project code.
   */
  public function run(): void
  {
    // Get current project code from environment
    $projectCode = env('CURRENT_PROJECT_CODE', 'hd001');

    $this->command->info(" Starting Project Database Seeding for project: {$projectCode}");

    // Ensure we're using the project connection
    $this->command->info('Database connection: '.config('database.default'));

    // Set project code in environment for child seeders
    putenv("CURRENT_PROJECT_CODE={$projectCode}");

    // Run seeders in correct order (categories first, then brands, then products)
    $this->call([
      ProjectProductCategorySeeder::class,
      ProjectBrandSeeder::class,
      ProjectProductSeeder::class,
    ]);

    $this->command->info(' Project Database Seeding completed successfully!');
    $this->command->info('');
    $this->command->info(' Summary:');
    $this->command->info('  - Categories: '.ProjectProductCategory::count());
    $this->command->info('  - Brands: '.ProjectBrand::count());
    $this->command->info('  - Products: '.ProjectProduct::count());
    $this->command->info('');
    $this->command->info(' You can now test the ProductController index method at:');
    $this->command->info("  - /{$projectCode}/admin/products (Project context)");
    $this->command->info('');
    $this->command->info(' To seed different projects, run:');
    $this->command->info('  php artisan db:seed --class=ProjectDatabaseSeeder --project=abc123');
  }
}
