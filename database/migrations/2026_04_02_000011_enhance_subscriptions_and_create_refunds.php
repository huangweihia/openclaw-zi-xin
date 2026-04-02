<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 增强 subscriptions 表
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('auto_renew')->default('0')->after('status')->comment('是否自动续费：0/1');
            $table->timestamp('last_payment_at')->nullable()->after('auto_renew')->comment('最后支付时间');
            $table->timestamp('next_billing_at')->nullable()->after('last_payment_at')->comment('下次计费时间');
            $table->string('upgrade_from')->nullable()->after('next_billing_at')->comment('升级前套餐');
        });

        // 退款申请表
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->comment('退款金额');
            $table->string('reason')->comment('退款原因');
            $table->text('description')->nullable()->comment('详细描述');
            $table->string('status')->default('pending')->comment('状态：pending/approved/rejected');
            $table->text('audit_note')->nullable()->comment('审核备注');
            $table->foreignId('audited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('audited_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
            
            $table->index('status');
        });

        // 发票申请表
        Schema::create('invoice_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_type')->comment('发票类型：personal/company');
            $table->string('invoice_title')->comment('发票抬头');
            $table->string('tax_id')->nullable()->comment('税号');
            $table->string('invoice_email')->comment('接收邮箱');
            $table->string('status')->default('pending')->comment('状态：pending/processing/sent');
            $table->string('invoice_number')->nullable()->comment('发票号码');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['auto_renew', 'last_payment_at', 'next_billing_at', 'upgrade_from']);
        });
        Schema::dropIfExists('refund_requests');
        Schema::dropIfExists('invoice_requests');
    }
};
