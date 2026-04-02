<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmtpConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description',
        'is_encrypted',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
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
    public static function set($key, $value, $description = null, $isEncrypted = false)
    {
        $setting = self::where('key', $key)->first();
        
        if ($setting) {
            $setting->update([
                'value' => $value,
                'description' => $description ?? $setting->description,
                'is_encrypted' => $isEncrypted,
            ]);
        } else {
            self::create([
                'key' => $key,
                'value' => $value,
                'description' => $description,
                'is_encrypted' => $isEncrypted,
            ]);
        }
    }

    /**
     * 获取 SMTP 配置
     */
    public static function getSmtpConfig(): array
    {
        return [
            'host' => self::get('smtp_host', 'smtp.qq.com'),
            'port' => self::get('smtp_port', '465'),
            'username' => self::get('smtp_username', ''),
            'password' => self::get('smtp_password', ''),
            'encryption' => self::get('smtp_encryption', 'ssl'),
            'from_address' => self::get('smtp_from_address', ''),
            'from_name' => self::get('smtp_from_name', 'AI 副业情报局'),
        ];
    }
}
