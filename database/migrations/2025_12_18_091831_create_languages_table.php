<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tiếng Việt, English, etc.
            $table->string('code', 10)->unique(); // vi, en, etc.
            $table->string('locale', 10); // vi_VN, en_US, etc.
            $table->string('flag')->nullable(); // URL or emoji flag
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default languages
        DB::table('languages')->insert([
            [
                'name' => 'Tiếng Việt',
                'code' => 'vi',
                'locale' => 'vi_VN',
                'flag' => '🇻🇳',
                'is_default' => true,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'English',
                'code' => 'en',
                'locale' => 'en_US',
                'flag' => '🇺🇸',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
