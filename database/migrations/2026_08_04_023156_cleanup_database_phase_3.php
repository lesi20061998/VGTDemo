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

        // 1. Dọn dẹp Nhóm Media
        Schema::dropIfExists('media_files');
        Schema::dropIfExists('media_folders');
        Schema::dropIfExists('media_collections');
        Schema::dropIfExists('media_collection_items');

        // 2. Dọn dẹp Nhóm Ecommerce (Orders)
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('order_status_histories');

        // 3. Dọn dẹp Nhóm Theme
        Schema::dropIfExists('menus');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('archive_templates');
        Schema::dropIfExists('fonts');

        // 4. Dọn dẹp Nhóm Quản trị Nội bộ (HR/Tasks)
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('project_tickets');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('positions');

        // 5. Các Bảng Khác
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('projects_system_tables');
        Schema::dropIfExists('website_configs');
        Schema::dropIfExists('project_password_audits');

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
