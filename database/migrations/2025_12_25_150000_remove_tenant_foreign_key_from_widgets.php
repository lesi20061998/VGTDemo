<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Get all foreign keys on widgets table
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'widgets' 
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");
        
        Schema::table('widgets', function (Blueprint $table) use ($foreignKeys) {
            foreach ($foreignKeys as $fk) {
                try {
                    $table->dropForeign($fk->CONSTRAINT_NAME);
                } catch (\Exception $e) {
                    // Ignore if already dropped
                }
            }
        });
    }

    public function down(): void
    {
        // Don't restore foreign keys
    }
};
