<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class TestDatabaseErrorHandling extends Command
{
    protected $signature = 'test:database-error-handling';

    protected $description = 'Test database error handling for numeric overflow';

    public function handle()
    {
        $this->info('Testing database error handling...');

        try {
            // Thử tạo một record với giá trị quá lớn
            DB::table('products_enhanced')->insert([
                'name' => 'Test Product',
                'sku' => 'TEST-OVERFLOW-'.time(),
                'price' => 578567856785678, // Giá trị quá lớn
                'sale_price' => 123456789012345, // Giá trị quá lớn
                'language_id' => 1,
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->error('❌ Test failed - should have thrown an exception');

        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'Out of range value')) {
                $this->info('✅ Database correctly rejected oversized values');
                $this->info('Error message: '.$e->getMessage());
            } else {
                $this->error('❌ Unexpected database error: '.$e->getMessage());
            }
        } catch (\Exception $e) {
            $this->error('❌ Unexpected error: '.$e->getMessage());
        }

        // Test với giá trị hợp lệ
        try {
            $testId = DB::table('products_enhanced')->insertGetId([
                'name' => 'Test Valid Product',
                'sku' => 'TEST-VALID-'.time(),
                'price' => 1000000.50, // Giá trị hợp lệ
                'sale_price' => 800000.25, // Giá trị hợp lệ
                'language_id' => 1,
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info('✅ Valid values inserted successfully with ID: '.$testId);

            // Cleanup
            DB::table('products_enhanced')->where('id', $testId)->delete();
            $this->info('✅ Test data cleaned up');

        } catch (\Exception $e) {
            $this->error('❌ Failed to insert valid data: '.$e->getMessage());
        }

        $this->info('Database error handling test completed!');

        return 0;
    }
}
