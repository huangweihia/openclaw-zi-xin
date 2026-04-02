<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('profile_messages')) {
            Schema::create('profile_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete()->comment('主页主人');
                $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete()->comment('留言者');
                $table->text('body');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(['recipient_id', 'created_at']);
                $table->index('sender_id');
            });
        }

        if (!Schema::hasTable('vip_urgent_notification_logs')) {
            Schema::create('vip_urgent_notification_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sender_user_id')->constrained('users')->cascadeOnDelete()->comment('发起紧急邮件的 VIP（主页主人）');
                $table->foreignId('recipient_user_id')->constrained('users')->cascadeOnDelete()->comment('收件人（留言者）');
                $table->foreignId('profile_message_id')->constrained('profile_messages')->cascadeOnDelete();
                $table->timestamp('sent_at');
                $table->timestamps();

                $table->index(['sender_user_id', 'sent_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vip_urgent_notification_logs');
        Schema::dropIfExists('profile_messages');
    }
};
