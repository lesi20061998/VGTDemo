<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archive_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('name');
            $table->string('type');  // product, post, category
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('template_code')->nullable();
            $table->longText('template_css')->nullable();
            $table->longText('template_js')->nullable();
            $table->json('config')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_default']);
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archive_templates');
    }
};
