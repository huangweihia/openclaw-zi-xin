<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new
class extends Migration
{
    public function up(): void
    {
        // 创建会员订阅表
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('plan', ['monthly', 'yearly', 'lifetime']);
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('payment_id', 100)->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('status');
        });

        // 创建订单表
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('product_type', ['subscription', 'service']);
            $table->unsignedBigInteger('product_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'paid', 'cancelled', 'refunded'])->default('pending');
            $table->string('payment_method', 50)->nullable();
            $table->timestamp('payment_time')->nullable();
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();
            
            $table->index('order_no');
            $table->index('user_id');
            $table->index('status');
        });

        // 创建分类表
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('sort')->default(0);
            $table->boolean('is_premium')->default(false);
            $table->timestamps();
        });

        // 创建文章表
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('summary', 500)->nullable();
            $table->longText('content')->nullable();
            $table->string('cover_image')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('view_count')->default(0);
            $table->unsignedBigInteger('like_count')->default(0);
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->string('source_url')->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->timestamps();
            
            $table->index('slug');
            $table->index('category_id');
            $table->index('is_published');
            $table->index('is_premium');
        });

        // 创建项目表
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('full_name', 150)->nullable();
            $table->text('description')->nullable();
            $table->string('url')->unique();
            $table->string('language', 50)->nullable();
            $table->integer('stars')->default(0);
            $table->integer('forks')->default(0);
            $table->decimal('score', 5, 2)->default(0);
            $table->json('tags')->nullable();
            $table->text('monetization')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('collected_at')->useCurrent();
            $table->timestamps();
            
            $table->index('url');
            $table->index('stars');
            $table->index('score');
            $table->index('is_featured');
        });

        // 创建邮件订阅表
        Schema::create('email_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('token', 100)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamp('subscribed_at')->useCurrent();
            $table->timestamp('unsubscribed_at')->nullable();
        });

        // 创建系统设置表
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->enum('type', ['string', 'number', 'boolean', 'json'])->default('string');
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        // 创建任务批处理表
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('email_subscribers');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('job_batches');
    }
};
