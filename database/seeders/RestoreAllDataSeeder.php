<?php

namespace Database\Seeders;

use App\Models\EmailSetting;
use App\Models\EmailSubscription;
use App\Models\EmailTemplate;
use App\Models\SmtpConfig;
use Illuminate\Database\Seeder;

/**
 * 一键恢复「邮件侧」基础数据：复用 EmailConfigSeeder / EmailTemplatePresetSeeder，避免与迁移/预设 HTML 双份维护。
 * 全站业务演示数据请用 DatabaseSeeder 或 TestDataSeeder。
 */
class RestoreAllDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 恢复邮件相关基础数据（SMTP / 设置 / 模板 / 演示订阅）...');

        $this->call([
            EmailConfigSeeder::class,
            EmailTemplatePresetSeeder::class,
            DemoEmailSubscriptionsSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('✅ 完成');
        $this->command->info('   - SMTP 配置：'.SmtpConfig::count().' 项');
        $this->command->info('   - 邮件设置：'.EmailSetting::count().' 项');
        $this->command->info('   - 邮件模板：'.EmailTemplate::count().' 个');
        $this->command->info('   - 订阅记录：'.EmailSubscription::count().' 条');
    }
}
