<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->boolean('subscribed_to_daily')->default(true);  // 日报
            $table->boolean('subscribed_to_weekly')->default(true); // 周报
            $table->boolean('subscribed_to_notifications')->default(true); // 系统通知
            $table->string('unsubscribe_token')->unique(); // 退订令牌
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
            
            $table->index('email');
            $table->index('unsubscribe_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_subscriptions');
    }
};
