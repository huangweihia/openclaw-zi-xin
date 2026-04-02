<?php

use App\Models\EmailTemplate;
use Illuminate\Database\Migrations\Migration;

/**
 * 定时任务 emails:send-scheduled 依赖 key=daily_digest；此前仅 Seeder 写入，migrate 后线上易缺模板。
 * 与 database/data/email_templates_newsletter_and_system_presets.php 同源，便于与 EmailTemplatePresetSeeder 一致。
 */
return new class extends Migration
{
    public function up(): void
    {
        $templates = require __DIR__.'/../data/email_templates_newsletter_and_system_presets.php';

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['key' => $template['key']],
                $template
            );
        }
    }

    public function down(): void
    {
        $templates = require __DIR__.'/../data/email_templates_newsletter_and_system_presets.php';
        $keys = array_column($templates, 'key');
        EmailTemplate::whereIn('key', $keys)->delete();
    }
};
