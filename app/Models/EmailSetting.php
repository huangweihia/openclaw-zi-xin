<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    /**
     * 获取配置值
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * 设置配置值
     */
    public static function set($key, $value, $description = null)
    {
        $setting = self::where('key', $key)->first();
        
        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            self::create([
                'key' => $key,
                'value' => $value,
                'description' => $description,
            ]);
        }
    }

    /**
     * 获取所有邮件接收人
     */
    public static function getRecipients(): array
    {
        $setting = self::where('key', 'email_recipients')->first();
        if (!$setting) {
            return ['2801359160@qq.com'];
        }
        
        $decoded = json_decode($setting->value, true);
        return is_array($decoded) && !empty($decoded) ? $decoded : [$setting->value];
    }

    /**
     * 获取发送时间
     */
    public static function getSendTime(): string
    {
        return self::get('email_send_time', '10:00');
    }

    /**
     * 定时任务「日报」使用的邮件模板 key（对应 email_templates.key）
     */
    public static function getDigestTemplateKey(): string
    {
        $v = trim((string) self::get('email_digest_template_key', 'daily_digest'));

        return $v !== '' ? $v : 'daily_digest';
    }

    /**
     * 定时任务「周报 / 周一」使用的邮件模板 key
     */
    public static function getWeeklyTemplateKey(): string
    {
        $v = trim((string) self::get('email_weekly_template_key', 'weekly_summary'));

        return $v !== '' ? $v : 'weekly_summary';
    }
}
