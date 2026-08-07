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
        // 1. Thêm cột meta_data vào bảng posts (nếu chưa có)
        if (! Schema::hasColumn('posts', 'meta_data')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->json('meta_data')->nullable()->after('seo_data');
            });
        }

        // 2. Tạo bảng taxonomies (Gom chung Categories, Brands, Tags, v.v.)
        if (! Schema::hasTable('taxonomies')) {
            Schema::create('taxonomies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('project_id')->nullable()->index();
                $table->unsignedBigInteger('tenant_id')->nullable()->index();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('taxonomy')->index(); // e.g., 'category', 'product_cat', 'brand', 'tag'
                $table->text('description')->nullable();
                $table->unsignedBigInteger('parent_id')->nullable()->index();
                $table->integer('order')->default(0);
                $table->string('status')->default('active'); // active, inactive
                $table->json('meta_data')->nullable(); // Để mở rộng sau này (icon, image...)
                $table->timestamps();
            });
        }

        // 3. Tạo bảng term_relationships (Liên kết N-N giữa Posts và Taxonomies)
        if (! Schema::hasTable('term_relationships')) {
            Schema::create('term_relationships', function (Blueprint $table) {
                $table->unsignedBigInteger('object_id')->index(); // Post ID
                $table->unsignedBigInteger('term_taxonomy_id')->index(); // Taxonomy ID
                $table->integer('order')->default(0);

                $table->primary(['object_id', 'term_taxonomy_id']);

                // Lưu ý: Không dùng foreign key constraint cứng ở đây để dễ dàng thao tác / backup
                // vì hệ thống multisite/tenant thường hay gặp lỗi khi migrate nếu dùng foreign.
            });
        }

        // 4. Xóa các bảng dư thừa (Dọn dẹp DB)
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('project_product_categories');
        Schema::dropIfExists('project_products');
        Schema::dropIfExists('project_brands');
        Schema::dropIfExists('product_attribute_value_mappings');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('product_category_product');
        Schema::dropIfExists('brand_product');
        Schema::dropIfExists('product_variations');
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('products_enhanced');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('brands');
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('term_relationships');
        Schema::dropIfExists('taxonomies');

        if (Schema::hasColumn('posts', 'meta_data')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('meta_data');
            });
        }

        // Không thể rollback các bảng đã Drop (Trừ khi có backup DB)
    }
};
