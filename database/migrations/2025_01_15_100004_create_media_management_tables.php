<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Media Folders
        Schema::create('media_folders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('path');
            $table->timestamps();
            
            $table->foreign('parent_id')->references('id')->on('media_folders')->onDelete('cascade');
            $table->index(['parent_id', 'name']);
            $table->index(['path']);
        });

        // 2. Media Files
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type');
            $table->string('disk')->default('public');
            $table->string('path');
            $table->unsignedBigInteger('size');
            $table->json('metadata')->nullable(); // dimensions, duration, etc.
            $table->foreignId('folder_id')->nullable()->constrained('media_folders')->onDelete('set null');
            $table->string('alt_text')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['mime_type']);
            $table->index(['folder_id']);
            $table->index(['created_at']);
        });

        // 3. Media Collections (for organizing media)
        Schema::create('media_collections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 4. Media Collection Items
        Schema::create('media_collection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('media_collections')->onDelete('cascade');
            $table->foreignId('media_file_id')->constrained()->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->unique(['collection_id', 'media_file_id']);
            $table->index(['collection_id', 'sort_order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('media_collection_items');
        Schema::dropIfExists('media_collections');
        Schema::dropIfExists('media_files');
        Schema::dropIfExists('media_folders');
    }
};