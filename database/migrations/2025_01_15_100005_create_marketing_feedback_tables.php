<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Newsletter Subscribers
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->enum('status', ['pending', 'subscribed', 'unsubscribed'])->default('pending');
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('subscription_token')->nullable();
            $table->json('metadata')->nullable(); // source, tags, etc.
            $table->timestamps();
            
            $table->index(['status', 'subscribed_at']);
            $table->index(['email']);
        });

        // 2. Contact Forms
        Schema::create('contact_forms', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['general', 'demo_request', 'support'])->default('general');
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('subject');
            $table->text('message');
            $table->enum('status', ['new', 'in_progress', 'resolved', 'closed'])->default('new');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('internal_notes')->nullable();
            $table->json('metadata')->nullable(); // IP, user agent, etc.
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index(['type', 'status']);
            $table->index(['assigned_to']);
        });

        // 3. Feedback/Reviews System
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->enum('type', ['bug', 'feature', 'complaint', 'compliment', 'other'])->default('other');
            $table->string('subject');
            $table->text('message');
            $table->integer('rating')->nullable(); // 1-5 stars
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['new', 'in_progress', 'resolved', 'closed'])->default('new');
            $table->string('ticket_id')->unique();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('internal_notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'priority']);
            $table->index(['type', 'status']);
            $table->index(['assigned_to']);
            $table->index(['ticket_id']);
        });

        // 4. Feedback Responses
        Schema::create('feedback_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feedback_id')->constrained('feedbacks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('message');
            $table->boolean('is_internal')->default(false);
            $table->json('attachments')->nullable();
            $table->timestamps();
            
            $table->index(['feedback_id', 'created_at']);
        });

        // 5. Banners/Promotions
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image');
            $table->string('link_url')->nullable();
            $table->string('link_text')->nullable();
            $table->enum('position', ['hero', 'sidebar', 'footer', 'popup'])->default('hero');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->integer('clicks')->default(0);
            $table->integer('views')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'position', 'sort_order']);
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('banners');
        Schema::dropIfExists('feedback_responses');
        Schema::dropIfExists('feedbacks');
        Schema::dropIfExists('contact_forms');
        Schema::dropIfExists('newsletter_subscribers');
    }
};