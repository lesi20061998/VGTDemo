<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Các bảng được gom thành post_type
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('feedbacks');
        Schema::dropIfExists('feedback_responses');
        Schema::dropIfExists('contact_forms');
        Schema::dropIfExists('form_submissions');

        // 2. Các bảng được gom thành taxonomies
        Schema::dropIfExists('tags');
        Schema::dropIfExists('attribute_groups');

        // 3. Các bảng pivot tàn dư
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('product_attribute_product');
        Schema::dropIfExists('product_brand_pivot');
        Schema::dropIfExists('product_category_pivot');

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
