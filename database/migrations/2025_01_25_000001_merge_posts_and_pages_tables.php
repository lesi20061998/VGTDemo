<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Tắt kiểm tra khóa ngoại tạm thời
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Xóa bảng posts_unified nếu đã tồn tại
        Schema::dropIfExists('posts_unified');
        
        // Tạo bảng posts_unified mới
        Schema::create('posts_unified', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->enum('post_type', ['post', 'page'])->default('post');
            $table->string('template')->nullable(); // cho pages
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('seo_data')->nullable();
            $table->integer('views')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['post_type', 'status', 'published_at']);
            $table->index(['slug']);
            $table->index(['author_id']);
        });

        // Chuyển dữ liệu từ posts sang posts_unified
        if (Schema::hasTable('posts')) {
            DB::statement("
                INSERT INTO posts_unified (id, title, slug, excerpt, content, featured_image, post_type, status, meta_title, meta_description, seo_data, views, published_at, author_id, created_at, updated_at)
                SELECT id, title, slug, excerpt, content, featured_image, 'post', status, meta_title, meta_description, seo_data, views, published_at, author_id, created_at, updated_at
                FROM posts
            ");
        }

        // Chuyển dữ liệu từ pages sang posts_unified
        if (Schema::hasTable('pages')) {
            $maxPostId = DB::table('posts_unified')->max('id') ?? 0;
            DB::statement("
                INSERT INTO posts_unified (id, title, slug, content, post_type, template, status, meta_title, meta_description, seo_data, created_at, updated_at)
                SELECT id + {$maxPostId}, title, slug, content, 'page', template, status, meta_title, meta_description, seo_data, created_at, updated_at
                FROM pages
            ");
        }

        // Cập nhật page_sections để trỏ đến posts_unified (nếu có dữ liệu pages)
        if (Schema::hasTable('page_sections') && Schema::hasTable('pages')) {
            $maxPostId = DB::table('posts')->max('id') ?? 0;
            if ($maxPostId > 0) {
                DB::statement("UPDATE page_sections SET post_id = post_id + {$maxPostId}");
            }
        }

        // Xóa bảng cũ
        Schema::dropIfExists('pages');
        Schema::dropIfExists('posts');

        // Đổi tên bảng mới
        Schema::rename('posts_unified', 'posts');
        
        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        // Tạo lại bảng posts và pages riêng biệt
        Schema::create('posts_old', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('seo_data')->nullable();
            $table->integer('views')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('pages_old', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->string('template')->default('default');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('seo_data')->nullable();
            $table->timestamps();
        });

        // Chuyển dữ liệu ngược lại
        DB::statement("
            INSERT INTO posts_old (id, title, slug, excerpt, content, featured_image, status, meta_title, meta_description, seo_data, views, published_at, author_id, created_at, updated_at)
            SELECT id, title, slug, excerpt, content, featured_image, status, meta_title, meta_description, seo_data, views, published_at, author_id, created_at, updated_at
            FROM posts WHERE post_type = 'post'
        ");

        DB::statement("
            INSERT INTO pages_old (id, title, slug, content, template, status, meta_title, meta_description, seo_data, created_at, updated_at)
            SELECT id, title, slug, content, COALESCE(template, 'default'), status, meta_title, meta_description, seo_data, created_at, updated_at
            FROM posts WHERE post_type = 'page'
        ");

        Schema::dropIfExists('posts');
        Schema::rename('posts_old', 'posts');
        Schema::rename('pages_old', 'pages');

        // Khôi phục page_sections (giữ nguyên post_id)
    }
};