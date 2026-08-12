<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->nullable()->index();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();

            $table->string('reviewer_name');
            $table->string('reviewer_avatar')->nullable();
            $table->string('reviewer_title')->nullable()->comment('Chức danh / nghề nghiệp');

            $table->text('content');
            $table->unsignedTinyInteger('rating')->default(5)->comment('1-5 stars');
            $table->string('image')->nullable()->comment('Ảnh kèm đánh giá');

            $table->string('status')->default('approved')->comment('pending|approved|rejected');
            $table->unsignedInteger('sort_order')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
