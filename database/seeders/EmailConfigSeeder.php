<?php

namespace Database\Seeders;

use App\Models\SmtpConfig;
use App\Models\EmailSetting;
use Illuminate\Database\Seeder;

class EmailConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 初始化 SMTP 配置
        $smtpConfigs = [
            ['key' => 'smtp_host', 'value' => 'smtp.qq.com', 'description' => 'SMTP 服务器', 'is_encrypted' => false],
            ['key' => 'smtp_port', 'value' => '465', 'description' => 'SMTP 端口', 'is_encrypted' => false],
            ['key' => 'smtp_encryption', 'value' => 'ssl', 'description' => '加密方式', 'is_encrypted' => false],
            ['key' => 'smtp_username', 'value' => '2801359160@qq.com', 'description' => 'SMTP 用户名', 'is_encrypted' => false],
            ['key' => 'smtp_password', 'value' => 'uvxftlhiicvzdffa', 'description' => 'SMTP 密码/授权码', 'is_encrypted' => true],
            ['key' => 'smtp_from_address', 'value' => '2801359160@qq.com', 'description' => '发件邮箱', 'is_encrypted' => false],
            ['key' => 'smtp_from_name', 'value' => 'AI 副业情报局', 'description' => '发件人名称', 'is_encrypted' => false],
        ];

        foreach ($smtpConfigs as $config) {
            SmtpConfig::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }

        // 初始化邮件配置（与演示订阅邮箱对齐，便于本地联调）
        $emailSettings = [
            ['key' => 'email_recipients', 'value' => json_encode(['2801359160@qq.com', 'vip1@example.com', 'vip2@example.com']), 'description' => '邮件接收人列表'],
            ['key' => 'email_send_time', 'value' => '10:00', 'description' => '邮件发送时间'],
            ['key' => 'email_digest_template_key', 'value' => 'daily_digest', 'description' => '定时日报模板 key'],
            ['key' => 'email_weekly_template_key', 'value' => 'weekly_summary', 'description' => '定时周报（周一）模板 key'],
            ['key' => 'email_daily_enabled', 'value' => '1', 'description' => '是否启用日报'],
            ['key' => 'email_weekly_enabled', 'value' => '1', 'description' => '是否启用周报'],
        ];

        foreach ($emailSettings as $setting) {
            EmailSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        // 邮件 HTML 模板由迁移（2026_03_28_150000 等）与 EmailTemplatePresetSeeder 写入，避免此处简版覆盖富文本预设

        $this->command->info('✅ 邮件配置初始化完成！');
        $this->command->info('   - SMTP 配置：7 项');
        $this->command->info('   - 邮件设置：'.count($emailSettings).' 项');
    }
}
